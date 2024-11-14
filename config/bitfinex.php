<?php

return [
    "base_url" => "https://api-pub.bitfinex.com/v2/",
    "single_ticker_ext" => "ticker/{symbol}",
    "historical_ticker_ext" => "tickers/hist",
    "allowed_period_markers" => [
        "H",
        "D",
        "M",
        "Y"
    ],
    "symbol_replacement_flag" => "{symbol}",
    "symbols" => [
        "tBTCUSD",
        "tBTCEUR",
    ]
];