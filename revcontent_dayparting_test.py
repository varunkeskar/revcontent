import requests
from datetime import datetime

# === Config ===
ACCESS_TOKEN = "829b3f57686a3bfe3aabaced06e4a9a78a5dadce"
CAMPAIGN_IDS = ["2453224", "2448613"]

HEADERS = {
    "Authorization": f"Bearer {ACCESS_TOKEN}",
    "Content-Type": "application/json"
}

# === Dry-run toggle ===
DRY_RUN = False  # Change to True if you want to test without sending real API calls

def pause_campaign(campaign_id):
    url = f"https://api.revcontent.io/api/v1/campaigns/{campaign_id}/pause"
    if DRY_RUN:
        print(f"[DRY RUN] Would pause campaign {campaign_id} at {datetime.now()}")
    else:
        response = requests.post(url, headers=HEADERS)
        print(f"[{datetime.now()}] Paused {campaign_id}: {response.status_code} - {response.text}")

def resume_campaign(campaign_id):
    url = f"https://api.revcontent.io/api/v1/campaigns/{campaign_id}/resume"
    if DRY_RUN:
        print(f"[DRY RUN] Would resume campaign {campaign_id} at {datetime.now()}")
    else:
        response = requests.post(url, headers=HEADERS)
        print(f"[{datetime.now()}] Resumed {campaign_id}: {response.status_code} - {response.text}")

# === Test Run ===
if __name__ == "__main__":
    print("Testing pause and resume actions...\n")

    for cid in CAMPAIGN_IDS:
        pause_campaign(cid)
        resume_campaign(cid)

        