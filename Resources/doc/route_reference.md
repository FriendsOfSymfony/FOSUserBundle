Routes reference
================

| name | method | Schema | Host | Path |
| ---- | ------ | ------ | ---- | ---- |
| fos_user_security_login           | ANY      | ANY    | ANY  | /login |
| fos_user_security_check           | ANY      | ANY    | ANY  | /login_check |
| fos_user_security_logout          | ANY      | ANY    | ANY  | /logout |
| fos_user_profile_show             | GET      | ANY    | ANY  | /profile/ |
| fos_user_profile_edit             | ANY      | ANY    | ANY  | /profile/edit |
| fos_user_registration_register    | ANY      | ANY    | ANY  | /register/ |
| fos_user_registration_check_email | GET      | ANY    | ANY  | /register/check-email |
| fos_user_registration_confirm     | GET      | ANY    | ANY  | /register/confirm/{token} |
| fos_user_registration_confirmed   | GET      | ANY    | ANY  | /register/confirmed |
| fos_user_resetting_request        | GET      | ANY    | ANY  | /resetting/request |
| fos_user_resetting_send_email     | POST     | ANY    | ANY  | /resetting/send-email |
| fos_user_resetting_check_email    | GET      | ANY    | ANY  | /resetting/check-email |
| fos_user_resetting_reset          | GET/POST | ANY    | ANY  | /resetting/reset/{token} |
| fos_user_change_password          | GET/POST | ANY    | ANY  | /profile/change-password |
