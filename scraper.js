const puppeteer = require('puppeteer');

async function scrapeUrl(url) {
    let browser;

    try {
        const browser = await puppeteer.launch({
            headless: 'new',
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-gpu'
            ]
        });

        const page = await browser.newPage();
        await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

        await page.goto(url, {
            waitUntil: 'networkidle2',
            timeout: 15000
        });

        await page.waitForSelector('h1', {
            timeout: 15000
        }).catch(() => {});

        const content = await page.content();

        await browser.close();

        return {
            success: true,
            html: content
        };

    } catch (error) {
        if (browser) await browser.close();
        return {
            success: false,
            error: error.message
        };
    }
}


const url = process.argv[2];

scrapeUrl(url).then(result => {
    console.log(JSON.stringify(result));
});