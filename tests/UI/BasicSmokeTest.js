const puppeteer = require('puppeteer');
const appUrl = process.env.APP_URL || 'http://localhost';

(async() => {
    const browser = await puppeteer.launch({
        headless: "new",
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });
    const page = await browser.newPage();

try {
    console.log('Testing Home Page...');
    const response = await page.goto(appUrl, {waitUntil: 'networkidle2'});
    if (!response || !response.ok()) {
        throw new Error(`Home page returned HTTP ${response?.status() ?? 'unknown'}`);
    }

    const bodyText = await page.$eval('body', body => body.innerText);
    if (!bodyText.trim()) {
        throw new Error('Home page rendered an empty document');
    }
    const title = await page.title();
    console.log('Title:', title);

    console.log('Checking for login link...');
    const loginLink = await page.$('a[href="/login"]');
    if (loginLink) {
        console.log('Login link found');
    } else {
        console.log('Login link NOT found - might be using SPA routing');
        const links = await page.$$eval('a', as => as.map(a => a.href));
        console.log('All links:', links.filter(l => l.includes('login')));
    }

    console.log('Smoke test passed');
} catch (error) {
    console.error('Smoke test FAILED:', error);
    process.exit(1);
} finally {
    await browser.close();
    }
})();
