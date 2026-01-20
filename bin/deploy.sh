#!/usr/bin/env bash

# Deployment script for IamLab
# This script sets up a production server (Ubuntu LTS) and deploys the application.

set -e

BOLD="$(tput bold)"
GREEN="$(tput setaf 2)"
YELLOW="$(tput setaf 3)"
RED="$(tput setaf 1)"
NC="$(tput sgr0)"

function info() {
    echo "${GREEN}${BOLD}INFO: ${NC}$1"
}

function warn() {
    echo "${YELLOW}${BOLD}WARN: ${NC}$1"
}

function error() {
    echo "${RED}${BOLD}ERROR: ${NC}$1"
}

# --- Configuration ---
DEPLOY_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$DEPLOY_DIR/.." && pwd)"
APP_PORT=8065 # Production proxy port

# --- Prompt for server details ---
read -p "Server IP/Hostname: " SERVER_HOST
read -p "SSH User (default: root): " SERVER_USER
SERVER_USER=${SERVER_USER:-root}
read -p "Domain Name (e.g. example.com): " DOMAIN
read -p "Email for SSL/Certbot: " SSL_EMAIL
read -p "Git Repository URL: " GIT_REPO

# --- SSH Setup ---
info "Checking SSH connection..."
if ! ssh -o BatchMode=yes -o ConnectTimeout=5 "$SERVER_USER@$SERVER_HOST" exit 2>/dev/null; then
    warn "SSH key connection failed. Attempting to copy SSH key..."
    ssh-copy-id "$SERVER_USER@$SERVER_HOST" || { error "Failed to copy SSH key. Please ensure you have an SSH key generated (ssh-keygen) and you know the server password."; exit 1; }
fi

# --- Server Provisioning ---
info "Provisioning server..."

ssh -t "$SERVER_USER@$SERVER_HOST" "bash -s" <<EOF
set -e
info() { echo "\033[1;32mINFO: \033[0m\$1"; }

# 1. Update and Upgrade
info "Updating and upgrading packages..."
export DEBIAN_FRONTEND=noninteractive
apt-get update
apt-get upgrade -y

# 2. Install Prerequisites
info "Installing prerequisites..."
apt-get install -y apt-transport-https ca-certificates curl software-properties-common git nginx certbot python3-certbot-nginx gnupg lsb-release cron zsh

# 3. Install Oh My Zsh
if [ ! -d ~/.oh-my-zsh ]; then
    info "Installing Oh My Zsh..."
    sh -c "\$(curl -fsSL https://raw.githubusercontent.com/ohmyzsh/ohmyzsh/master/tools/install.sh)" "" --unattended
    chsh -s \$(which zsh) \$USER
fi

# 4. Install Docker
if ! command -v docker &> /dev/null; then
    info "Installing Docker..."
    curl -fsSL https://get.docker.com -o get-docker.sh
    sh get-docker.sh
    usermod -aG docker \$USER
fi

# 5. Uninstall unneeded apps
info "Uninstalling unneeded apps..."
apt-get remove -y apache2 || true
apt-get autoremove -y

# 6. Secure SSH
info "Securing SSH..."
# Disable password authentication
sed -i 's/#PasswordAuthentication yes/PasswordAuthentication no/' /etc/ssh/sshd_config
sed -i 's/PasswordAuthentication yes/PasswordAuthentication no/' /etc/ssh/sshd_config
# Ensure PubkeyAuthentication is enabled
sed -i 's/#PubkeyAuthentication yes/PubkeyAuthentication yes/' /etc/ssh/sshd_config
systemctl restart ssh

# 7. Setup Directory & Git Deploy Key
APP_PATH="/home/\$USER/$(basename "$GIT_REPO" .git)"
if [ "\$USER" == "root" ]; then
    APP_PATH="/root/$(basename "$GIT_REPO" .git)"
fi

if [ ! -f ~/.ssh/id_rsa_deploy ]; then
    info "Setting up Git deploy key..."
    mkdir -p ~/.ssh
    ssh-keygen -t rsa -b 4096 -f ~/.ssh/id_rsa_deploy -N ""
    echo "------------------------------------------------------------"
    echo "Please add the following public key to your Git repository as a deploy key:"
    cat ~/.ssh/id_rsa_deploy.pub
    echo "------------------------------------------------------------"
    echo "Press ENTER once you have added the key to your Git provider..."
    read
    
    GIT_HOST=\$(echo "$GIT_REPO" | sed -E 's/.*@([^:]+).*/\1/')
    if [[ "\$GIT_REPO" == http* ]]; then
        GIT_HOST=\$(echo "$GIT_REPO" | sed -E 's/https?:\/\/([^\/]+).*/\1/')
    fi
    
    if [ ! -f ~/.ssh/config ] || ! grep -q "\$GIT_HOST" ~/.ssh/config; then
        cat >> ~/.ssh/config <<SSHCONFIG
Host \$GIT_HOST
    HostName \$GIT_HOST
    IdentityFile ~/.ssh/id_rsa_deploy
    StrictHostKeyChecking no
SSHCONFIG
    fi
fi

if [ ! -d "\$APP_PATH" ]; then
    info "Cloning repository..."
    git clone "$GIT_REPO" "\$APP_PATH"
else
    info "Updating repository..."
    cd "\$APP_PATH"
    git pull
fi

cd "\$APP_PATH"

# 8. Setup .env
if [ ! -f .env ]; then
    info "Setting up .env..."
    cp .env.example .env
    sed -i "s|APP_URL=.*|APP_URL=https://$DOMAIN|" .env
    sed -i "s|APP_PORT=.*|APP_PORT=$APP_PORT|" .env
    
    echo "Please enter database password (will be set in .env):"
    read -s DB_PASS
    sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=\$DB_PASS|" .env
fi

# 9. Deploy with phalcons
info "Running deployment steps via ./phalcons..."
chmod +x phalcons
./phalcons up -d
./phalcons composer install --no-dev
./phalcons migrate
./phalcons npm install
./phalcons npm run prod

# 10. Configure Nginx
info "Configuring Nginx..."
NGINX_CONF="/etc/nginx/sites-available/$DOMAIN"

# Bootstrap Nginx (Port 80 only for Certbot)
cat > \$NGINX_CONF <<BOOTSTRAP_EOF
server {
    listen 80;
    listen [::]:80;
    server_name $DOMAIN www.$DOMAIN;
    root \$APP_PATH/public;
    location / {
        proxy_pass http://localhost:$APP_PORT;
        proxy_set_header Host \\\$host;
    }
}
BOOTSTRAP_EOF

ln -sf \$NGINX_CONF /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl reload nginx

# 11. SSL with Certbot
info "Setting up SSL..."
certbot --nginx -d $DOMAIN -d www.$DOMAIN --non-interactive --agree-tos -m $SSL_EMAIL

# Now apply the full config
cat > \$NGINX_CONF <<'INNER_EOF'
$(cat "$DEPLOY_DIR/deploy/engineX.conf")
INNER_EOF

# Update Nginx config with correct domain and paths
sed -i "s/{{DOMAIN}}/$DOMAIN/g" \$NGINX_CONF
sed -i "s|{{APP_PATH}}|\$APP_PATH|g" \$NGINX_CONF
sed -i "s|{{APP_PORT}}|$APP_PORT|g" \$NGINX_CONF

nginx -t
systemctl reload nginx

# 12. Setup Cron
info "Setting up Cron..."
CRON_TEMP="\$(cat <<'CRON_EOF'
$(cat "$DEPLOY_DIR/deploy/cron.template")
CRON_EOF
)"
echo "\${CRON_TEMP//\{\{APP_PATH\}\}/\$APP_PATH}" > /etc/cron.d/phalcon-app
chmod 0644 /etc/cron.d/phalcon-app

# 13. Run User Scripts
info "Checking for user scripts..."
USER_SCRIPT_TEMP="\$(cat <<'USER_SCRIPT_EOF'
$(cat "$DEPLOY_DIR/deploy/user_scripts.template")
USER_SCRIPT_EOF
)"
# Replace variables and execute
USER_SCRIPT="\${USER_SCRIPT_TEMP//\{\{APP_PATH\}\}/\$APP_PATH}"
USER_SCRIPT="\${USER_SCRIPT//\{\{DOMAIN\}\}/$DOMAIN}"
USER_SCRIPT="\${USER_SCRIPT//\{\{APP_PORT\}\}/$APP_PORT}"
USER_SCRIPT="\${USER_SCRIPT//\{\{SERVER_USER\}\}/$SERVER_USER}"
USER_SCRIPT="\${USER_SCRIPT//\{\{SERVER_HOST\}\}/$SERVER_HOST}"

eval "\$USER_SCRIPT"

info "Deployment complete for $DOMAIN!"
EOF
