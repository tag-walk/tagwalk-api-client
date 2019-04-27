# TagwalkApiClientBundle

## Configuration reference

Create a file in config/packages/tagwalk_api_client.yaml

```yaml
tagwalk_api_client:
    api:
        host_url: "%env(TAGWALK_API_URL)%"
        client_id: "%env(TAGWALK_API_CLIENT_ID)%"
        client_secret: "%env(TAGWALK_API_CLIENT_SECRET)%"
        host_url: "%env(HOST_URL)%"
        timeout: 10
        cache_ttl: 600
        cache_directory: ""
        analytics: true
```
Add the following environment variables to your system or to your `.env` file:

```dotenv
TAGWALK_API_URL=https://api.tag-walk.com
TAGWALK_API_CLIENT_ID=your_client_id_token
TAGWALK_API_CLIENT_SECRET=your_client_secret_token
HOST_URL=https://domain.tld
```
