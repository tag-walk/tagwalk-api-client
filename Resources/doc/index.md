# TagwalkApiClientBundle

## Configuration reference

Create a file in config/packages/tagwalk_api_client.yaml

```yaml
tagwalk_api_client:
    host_url: "%env(TAGWALK_API_URL)%"
    client_id: "%env(TAGWALK_API_CLIENT_ID)%"
    client_secret: "%env(TAGWALK_API_CLIENT_SECRET)%"
    timeout: 10
    analytics: false
    light: true
    cache: 'cache.tagwalk_api'
    access_token_storage: 'oauth.storage.access_token'
    refresh_token_storage: 'oauth.storage.refresh_token'
    storage_prefix: 'tagwalk.'
```

Add the following environment variables to your system or to your `.env` file:

```dotenv
TAGWALK_API_URL=https://test.api.tag-walk.com
TAGWALK_API_CLIENT_ID=your_client_id_token
TAGWALK_API_CLIENT_SECRET=your_client_secret_token
```

| Parameter             | Optional | Description                                                |
| --------------------  | -------- | ---------------------------------------------------------- |
| host_url              | no       | URL of the Tagwalk API
| client_id             | no       | Client ID used to authenticate to the API using OAUTH client_credentials grant type
| client_secret         | no       | Client secret used to authenticate to the API using OAUTH client_credentials grant type
| timeout               | no       | HTTP request timeout in seconds
| analytics             | no       |
| light                 | no       |
| cache                 | yes      | Name of the symfony cache pool service that must be used to cache API responses, use a NullAdapter by default
| access_token_storage  | yes      | Name of the symfony cache pool service that must be used to store API access tokens, use a NullAdapter by defaul
| refresh_token_storage | yes      | Name of the symfony cache pool service that must be used to store API refresh tokens, use a NullAdapter by default
| storage_prefix        | yes      | Prefix each API cache, access token and refresh token key by this namespace
