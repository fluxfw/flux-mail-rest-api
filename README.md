# flux-mail-rest-api

Mail Rest Api for fetch or send mails

## Environment variables

First look at [flux-mail-api](https://github.com/fluxfw/flux-mail-api#environment-variables)

| Variable | Description | Default value |
| -------- | ----------- | ------------- |
| FLUX_MAIL_REST_API_SERVER_HTTPS_CERT | Path to HTTPS certificate file<br>Set this will enable listen on HTTPS<br>Should be on a volume | *-* |
| FLUX_MAIL_REST_API_SERVER_HTTPS_KEY | Path to HTTPS key file<br>Should be on a volume | *-* |
| FLUX_MAIL_REST_API_SERVER_LISTEN | Listen IP | 0.0.0.0 |
| FLUX_MAIL_REST_API_SERVER_PORT | Listen port | 9501 |

Minimal variables required to set are **bold**

## Example

[examples](examples)
