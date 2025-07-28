// Debug script to check AuthService state
// Run this in browser console to diagnose authentication issues

console.log('=== AuthService Debug Information ===');

// Check if AuthService is available
if (typeof AuthService !== 'undefined') {
    console.log('✅ AuthService is available');
    
    // Check authentication state
    console.log('Authentication State:');
    console.log('- isAuthenticated:', AuthService.isAuthenticated);
    console.log('- accessToken:', AuthService.accessToken ? 'Present' : 'Missing');
    console.log('- refreshToken:', AuthService.refreshToken ? 'Present' : 'Missing');
    console.log('- isLoggedIn():', AuthService.isLoggedIn());
    
    // Check user data
    console.log('\nUser Data:');
    const user = AuthService.getUser();
    console.log('- user object:', user);
    console.log('- user.roles:', user?.roles);
    console.log('- hasRole("admin"):', AuthService.hasRole('admin'));
    console.log('- isAdmin():', AuthService.isAdmin());
    console.log('- getUserRoles():', AuthService.getUserRoles());
    
    // Check localStorage
    console.log('\nLocalStorage Data:');
    console.log('- access_token:', localStorage.getItem('access_token') ? 'Present' : 'Missing');
    console.log('- refresh_token:', localStorage.getItem('refresh_token') ? 'Present' : 'Missing');
    console.log('- user:', localStorage.getItem('user') ? 'Present' : 'Missing');
    
    if (localStorage.getItem('user')) {
        try {
            const storedUser = JSON.parse(localStorage.getItem('user'));
            console.log('- stored user roles:', storedUser?.roles);
        } catch (e) {
            console.log('- stored user: Parse error');
        }
    }
    
    // Test API call
    console.log('\nTesting API call to /auth/user...');
    AuthService.getCurrentUser()
        .then(response => {
            console.log('✅ API call successful:', response);
            console.log('- Response roles:', response?.roles);
        })
        .catch(error => {
            console.log('❌ API call failed:', error);
        });
        
} else {
    console.log('❌ AuthService is not available');
    console.log('Available globals:', Object.keys(window));
}

console.log('=== End Debug Information ===');