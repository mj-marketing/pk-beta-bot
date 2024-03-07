import json
import os
import hashlib
from dotenv import load_dotenv
from telethon import TelegramClient, events

# Load environment variables
load_dotenv()
api_id = os.getenv('API_ID')
api_hash = os.getenv('API_HASH')
channels = os.getenv('CHANNELS').split(',')
blacklisted_domains = os.getenv('BLACKLISTED_DOMAINS').split(',')
logging_enabled = os.getenv('LOGGING_ENABLED') == 'True'
save_directory = os.getenv('SAVE_DIRECTORY')
base_url = os.getenv('BASE_URL')

# Initialize the client and ensure the save directory exists
client = TelegramClient('session', api_id, api_hash)
os.makedirs(save_directory, exist_ok=True)

def generate_filename(channel, message_id, extension=""):
    hash_input = f"{channel}_{message_id}".encode()
    return hashlib.sha256(hash_input).hexdigest() + extension

async def process_message(event):
    message = event.message
    channel_name = await event.get_chat()

    # Check if the message has an image and text
    if message.photo and message.text:
        # Generate a unique filename for the image
        image_filename = generate_filename(channel_name.title, message.id, ".jpg")
        image_file_path = os.path.join(save_directory, image_filename)
        await client.download_media(message.photo, file=image_file_path)

        message_data = {
            'channel': channel_name.title,
            'message_id': message.id,
            'image_url': os.path.join(base_url, image_filename),
            'text': message.text
        }

        # Check for buttons with URLs
        if message.buttons:
            for button_row in message.buttons:
                for button in button_row:
                    if button.url and not any(domain in button.url for domain in blacklisted_domains):
                        message_data['button_url'] = button.url
                        break

        # Generate a unique filename for the JSON file
        json_filename = generate_filename(channel_name.title, message.id, ".json")
        json_file_path = os.path.join(save_directory, json_filename)

        # Save to JSON file
        with open(json_file_path, 'w') as file:
            json.dump(message_data, file, indent=4)

        if logging_enabled:
            print(f"Saved message to {json_file_path}")

# Register the event handler for new messages
for channel in channels:
    client.add_event_handler(process_message, events.NewMessage(chats=channel))

with client:
    client.start()
    print("Client started. Listening for incoming messages...")
    client.run_until_disconnected()
