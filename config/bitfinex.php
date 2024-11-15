<?php

return [
    "base_url" => "https://api-pub.bitfinex.com/v2/",
    "single_ticker_ext" => "ticker/{symbol}",
    "historical_ticker_ext" => "tickers/hist",
    "symbol_replacement_flag" => "{symbol}",
    "symbols" => [
        "tBTCUSD",
        "tBTCEUR",
    ],
    "max_limit" => 250,
    "max_requests_per_minute" => 90
];
