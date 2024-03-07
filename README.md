# PKBetaBot - PHP Telegram Bot

## Description

PKBetaBot is a PHP-based Telegram bot designed for processing JSON files and automatically posting content to Telegram channels. It is useful for sharing updates, promotions, or notifications.

## Installation

Clone the repository and install dependencies:

```bash
git clone https://github.com/yourusername/pk-beta-bot.git
cd pk-beta-bot
composer install
```

## Configuration

Copy the `.env.example` file to `.env` and update the values:

```bash
cp .env.example .env
```

Edit `.env` to set your Telegram bot token, Amazon referral tag, and other settings.

## Environment Variables

To run this project, you will need to add the following environment variables to your `.env` file. Create this file in the root directory of the project if it does not already exist.

`DEBUG_MODE`
- Enables or disables debug mode.
- Set to `true` for development to display error messages.
- Set to `false` in production to hide error messages.

`LOGGING_ENABLED`
- Enables or disables logging.
- Set to `true` to enable logging of messages.
- Set to `false` to disable logging.

`CRON_INTERVAL`
- Specifies the interval (in seconds) for the cron job to check for new JSON files.
- Example: `60` for checking every minute.

`TELEGRAM_BOT_TOKEN`
- Your Telegram Bot API token.
- Obtain this token from the BotFather on Telegram.

`JSON_DIRECTORY`
- The directory path where your JSON files are located.
- Example: `/path/to/your/json/directory`.

`REFERRAL_TAG`
- Your Amazon referral tag for Amazon URLs.
- Example: `your_amazon_referral_tag`.

`AMAZON_BUTTON_TEXT`
- Text for the Amazon button in Telegram messages.
- Example: `View on Amazon`.

`WEBSITE_BUTTON_TEXT`
- Text for your website button in Telegram messages.
- Example: `Visit Our Website`.

`WEBSITE_URL`
- The URL of your website for the website button in Telegram messages.
- Example: `https://www.yourwebsite.com`.


## Running the Bot

Set up a cron job to execute `cron.php` at your desired interval, or run it manually:

```bash
php cron.php
```

## License

This project is licensed under the MIT License - see the LICENSE file for details.
