# Client Integration & Base URL

Integrating your client application (PHP, Node.js, Python, Mobile App, Bot, or Frontend) with the reverse proxy requires only changing the **Base URL**.

---

## 1. Determining Your Base URL

### Scenario A: Root Domain / Subdomain Deployment
If you deployed the proxy at the root of a domain (e.g. `https://api.yourdomain.com/`):

- **Original MarketApp URL:** `https://api.marketapp.org/`
- **New Proxy Base URL:** `https://api.yourdomain.com/`

### Scenario B: Subdirectory / Subfolder Deployment
If you deployed the proxy inside a subfolder (e.g. `https://yourdomain.com/marketapp/`):

- **Original MarketApp URL:** `https://api.marketapp.org/`
- **New Proxy Base URL:** `https://yourdomain.com/marketapp/`

---

## 2. Code Examples

=== "cURL (CLI)"
    ```bash
    # GET Request through Proxy
    curl -i -X GET "https://yourdomain.com/marketapp/v1/user/profile" \
      -H "Authorization: Bearer YOUR_TOKEN" \
      -H "Accept: application/json"

    # POST Request through Proxy
    curl -i -X POST "https://yourdomain.com/marketapp/v1/orders" \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer YOUR_TOKEN" \
      -d '{"product_id": 123, "quantity": 1}'
    ```

=== "JavaScript / Node.js (Axios)"
    ```javascript
    import axios from 'axios';

    const apiClient = axios.create({
      baseURL: 'https://yourdomain.com/marketapp/v1',
      headers: {
        'Authorization': 'Bearer YOUR_API_TOKEN',
        'Content-Type': 'application/json'
      }
    });

    // Make calls as usual
    const response = await apiClient.get('/user/profile');
    console.log(response.data);
    ```

=== "PHP (Guzzle)"
    ```php
    use GuzzleHttp\Client;

    $client = new Client([
        'base_uri' => 'https://yourdomain.com/marketapp/v1/',
        'headers' => [
            'Authorization' => 'Bearer YOUR_API_TOKEN',
            'Accept' => 'application/json',
        ],
    ]);

    $response = $client->request('GET', 'user/profile');
    echo $response->getBody();
    ```

=== "Python (Requests)"
    ```python
    import requests

    BASE_URL = "https://yourdomain.com/marketapp/v1"
    headers = {
        "Authorization": "Bearer YOUR_API_TOKEN",
        "Accept": "application/json"
    }

    response = requests.get(f"{BASE_URL}/user/profile", headers=headers)
    print(response.json())
    ```
