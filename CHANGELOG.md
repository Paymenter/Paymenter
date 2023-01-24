# Changelog
This file is a running track of new features and fixes to each version of the panel released starting with `v0.4.0`.

This project follows [Semantic Versioning](http://semver.org) guidelines.

## v1.11.2
### Changed
* Telemetry no longer sends a map of Egg and Nest UUIDs to the number of servers using them.
* Increased the timeout for the decompress files endpoint in the client API from 15 seconds to 15 minutes.

### Fixed
* Fixed Panel Docker image having a `v` prefix in the version displayed in the admin area.
* Fixed emails using the wrong queue name, causing them to not be sent.
* Fixed the settings keys used for configuring SMTP settings, causing settings to not save properly.
* Fixed the `MAIL_EHLO_DOMAIN` environment variable not being properly backwards compatible with the old `SERVER_NAME` variable.
