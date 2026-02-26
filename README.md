## Εγκατάσταση

1. Αντιγραφή φακέλου `currency-converter-nx` στο `wp-content/plugins/` ή clone.
2. (Προαιρετικά) Settings > Currency Converter όπου μπαίνει το API key απο το [exchangerate-api.com](https://www.exchangerate-api.com/). Χωρίς API key το plugin δουλεύει με στατικά mock data.

## Χρήση

### Widget

Appearance > Widgets > προσθεσε το "Currency Converter NXCode" σε οποιοδήποτε sidebar. Στις ρυθμίσεις του widget ορίζεις τίτλο και προεπιλεγμένα νομίσματα (from/to).

### Shortcode

Βασική χρήση:

```
[cc_nxcode]
```

Με παραμέτρους:

```
[cc_nxcode from="GBP" to="JPY"]
```

Παράμετροι:

| Παράμετρος | Προεπιλογή | Περιγραφή |
|------------|------------|-----------|
| `from` | USD | Κωδ. νομίσματος |
| `to` | EUR | Κωδ. νομίσματος |

## REST API

Το plugin έχει ένα endpoint για τις ισοτιμίες:

```
GET /wp-json/cc-nxcode/v1/rates?base=USD
```

Η παράμετρος `base` είναι προαιρετική (προεπιλογή: USD σε EUR). Πρέπει να είναι τριψήφιος κωδικός νομίσματος (π.χ. EUR, GBP). Τα αποτελέσματα κρατούνται σε cache για 1 ωρα (transient).

Το response περιλαμβάνει:

- `base` -- το νόμισμα βάσης
- `rates` -- αντικείμενο με ισοτιμίες
- `last_updated` -- ημερομηνία τελευταίας ενημέρωσης
- `source` -- `live` αν χρησιμοποιείται API key, `mock` αν χρησιμοποιούνται τα ενσωματωμένα data

## Δομή αρχείων

```
currency-converter-nx/
  currency-converter-nxcode.php   -- Κύριο αρχείο plugin
  includes/
    class-cc-nxcode-settings.php  -- Σελίδα ρυθμίσεων (admin)
    class-cc-nxcode-rest-api.php  -- REST API endpoint
    class-cc-nxcode-widget.php    -- WordPress widget
  assets/build/
    currency-converter.js         -- Compiled React app
    currency-converter.css        -- Styles
  react-app/                      -- Πηγαίος κώδικας React (dev)
```

## Build React app

```bash
cd react-app
npm install
npm run build
```

Το build output πηγαίνει στο `assets/build/`.

## Ρυθμίσεις

Η μόνη ρύθμιση είναι το ExchangeRate-API key. Χωρίς αυτό, το plugin λειτουργεί κανονικά με σταθερές ισοτιμίες (mock data). Με έγκυρο API key, φέρνει πραγματικές ισοτιμίες και τις αποθηκεύει σε cache για 1 ωρα.

Settings > Currency Converter > ExchangeRate-API Key

## Υποστηριζόμενα νομίσματα

USD, EUR, GBP, JPY, AUD, CAD, CHF, CNY, SEK, NZD, MXN, SGD, HKD, NOK, KRW, TRY, INR, BRL, ZAR, PLN, DKK, THB, ILS, PHP, AED, SAR, MYR, RON, CZK, HUF, BGN, ISK, HRK, RUB, TWD, COP, CLP.