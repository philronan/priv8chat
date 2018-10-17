**Live demo: [https://chat.justmyl.uk/](https://chat.justmyl.uk/)**

A few extra notes on installation:
----------------------------------

The file `confirmation-email.php` won't work unless the server is configured with the ability to send emails from `robot@«your-domain.com»`. You'll need to set up DKIM and SPF records too, otherwise the emails will probably be flagged as spam (or deleted altogether).

Sorry about the messy code.
