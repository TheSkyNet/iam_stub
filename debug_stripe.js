const puppeteer = require('puppeteer');

(async () => {
    console.log('--- STRIPE CONSOLE DEBUGGER ---');
    console.log('Starting browser to inspect: http://localhost:8080/#!/demo/stripe');
    
    let browser;
    try {
        // Launch Chrome if available to capture more realistic noise
        browser = await puppeteer.launch({
            headless: 'new',
            executablePath: '/usr/bin/google-chrome',
            args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-web-security']
        }).catch(async () => {
             // Fallback to default if chrome not found
             return await puppeteer.launch({
                headless: 'new',
                args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-web-security']
            });
        });
        
        const page = await browser.newPage();
        
        // Inject a script to capture calls to window.showToast and prevent its definition
        await page.evaluateOnNewDocument(() => {
            let _showToast;
            Object.defineProperty(window, 'showToast', {
                set: function(val) {
                    console.log(`[DEBUG] window.showToast is being defined.`);
                    _showToast = function(message, type) {
                        console.log(`[TOAST_TRIGGERED] Type: ${type}, Message: ${JSON.stringify(message)}`);
                        return val.apply(this, arguments);
                    };
                },
                get: function() {
                    return _showToast;
                },
                configurable: true
            });
        });

        // Listen to console messages
        page.on('console', msg => {
            console.log(`[CONSOLE_${msg.type().toUpperCase()}] ${msg.text()}`);
        });

        // Listen for browser-side errors
        page.on('pageerror', err => {
            console.log(`[PAGE_ERROR] ${err.toString()}`);
        });

        // Listen for request failures
        page.on('requestfailed', request => {
            console.log(`[REQUEST_FAILED] ${request.url()} - ${request.failure().errorText}`);
        });

        // Listen for frame attachment
        page.on('frameattached', frame => {
            console.log(`[FRAME_ATTACHED] ${frame.url()}`);
        });

        // Set a long timeout for navigation as we want to capture async noise
        console.log('Navigating to http://localhost:8080/#!/demo/stripe...');
        await page.goto('http://localhost:8080/#!/demo/stripe', { 
            waitUntil: 'networkidle0',
            timeout: 60000 
        });

        // Wait for Mithril to render the content
        console.log('Waiting for Mithril to render #app...');
        try {
            await page.waitForSelector('#app', { timeout: 30000 });
            console.log('Mithril #app found.');
        } catch (e) {
            console.warn('Timed out waiting for #app.');
        }

        // Wait a bit more for the specific route to render
        await new Promise(r => setTimeout(r, 5000));

        // Check if we are on the right page
        const title = await page.title();
        const content = await page.content();
        console.log(`PAGE TITLE: ${title}`);
        console.log(`PAGE CONTENT SIZE: ${content.length}`);
        
        if (content.includes('Stripe')) {
            console.log('>>> CONFIRMED: Page contains "Stripe"');
        } else {
            console.warn('>>> WARNING: "Stripe" not found in page content. We might be on the wrong route.');
        }

        console.log('Waiting 15 seconds to capture all third-party noise (Stripe/hCaptcha/Fonts)...');
        await new Promise(r => setTimeout(r, 15000));
        
        console.log('--- DEBUGGING COMPLETE ---');
    } catch (e) {
        console.error('CRITICAL ERROR in debugger:', e.message);
    } finally {
        if (browser) await browser.close();
    }
})();
