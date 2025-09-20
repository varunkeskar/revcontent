import requests

# === Revcontent OAuth Credentials ===
CLIENT_ID = "vanceperformance"
CLIENT_SECRET = "30c8f47ac409cbdbda78e05d7aa25102785f9405"

# === Token Endpoint ===
TOKEN_URL = "https://api.revcontent.io/oauth/token"

# === Get Token ===
def get_access_token():
    headers = {
        "Content-Type": "application/x-www-form-urlencoded"
    }

    data = {
        "grant_type": "client_credentials",
        "client_id": CLIENT_ID,
        "client_secret": CLIENT_SECRET
    }

    print(f"🔐 Requesting token from {TOKEN_URL}...\n")

    response = requests.post(TOKEN_URL, headers=headers, data=data)

    print(f"🔍 Status code: {response.status_code}")
    print(f"🧾 Response: {response.text}")

    if response.status_code == 200:
        access_token = response.json().get("access_token")
        print(f"\n✅ Access token retrieved:\n{access_token}")
        return access_token
    else:
        print("\n❌ Failed to get token.")
        return None

# Run test
if __name__ == "__main__":
    get_access_token()