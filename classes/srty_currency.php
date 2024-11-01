<?php

class Srty_currency {

    public function iso4217()
    {
        return array(
            'ALL' => array('Albania Lek', 'Lek'),
            'AFN' => array('Afghanistan Afghani', '؋'),
            'ARS' => array('Argentina Peso', '$'),
            'AWG' => array('Aruba Guilder', 'ƒ'),
            'AUD' => array('Australia Dollar', '$'),
            'AZN' => array('Azerbaijan New Manat', 'ман'),
            'BSD' => array('Bahamas Dollar', '$'),
            'BBD' => array('Barbados Dollar', '$'),
            'BYR' => array('Belarus Ruble', 'p.'),
            'BZD' => array('Belize Dollar', 'BZ$'),
            'BMD' => array('Bermuda Dollar', '$'),
            'BOB' => array('Bolivia Boliviano', '$b'),
            'BAM' => array('Bosnia and Herzegovina Convertible Marka', 'KM'),
            'BWP' => array('Botswana Pula', 'P'),
            'BGN' => array('Bulgaria Lev', 'лв'),
            'BRL' => array('Brazil Real', 'R$'),
            'BND' => array('Brunei Darussalam Dollar', '$'),
            'KHR' => array('Cambodia Riel', '៛'),
            'CAD' => array('Canada Dollar', '$'),
            'KYD' => array('Cayman Islands Dollar', '$'),
            'CLP' => array('Chile Peso', '$'),
            'CNY' => array('China Yuan Renminbi', '¥'),
            'COP' => array('Colombia Peso', '$'),
            'CRC' => array('Costa Rica Colon', '₡'),
            'HRK' => array('Croatia Kuna', 'kn'),
            'CUP' => array('Cuba Peso', '₱'),
            'CZK' => array('Czech Republic Koruna', 'Kč'),
            'DKK' => array('Denmark Krone', 'kr'),
            'DOP' => array('Dominican Republic Peso', 'RD$'),
            'XCD' => array('East Caribbean Dollar', '$'),
            'EGP' => array('Egypt Pound', '£'),
            'SVC' => array('El Salvador Colon', '$'),
            'EEK' => array('Estonia Kroon', 'kr'),
            'EUR' => array('Euro Member Countries', '€'),
            'FKP' => array('Falkland Islands (Malvinas) Pound', '£'),
            'FJD' => array('Fiji Dollar', '$'),
            'GHC' => array('Ghana Cedi', '¢'),
            'GIP' => array('Gibraltar Pound', '£'),
            'GTQ' => array('Guatemala Quetzal', 'Q'),
            'GGP' => array('Guernsey Pound', '£'),
            'GYD' => array('Guyana Dollar', '$'),
            'HNL' => array('Honduras Lempira', 'L'),
            'HKD' => array('Hong Kong Dollar', '$'),
            'HUF' => array('Hungary Forint', 'Ft'),
            'ISK' => array('Iceland Krona', 'kr'),
            'INR' => array('India Rupee', '₹'),
            'IDR' => array('Indonesia Rupiah', 'Rp'),
            'IRR' => array('Iran Rial', '﷼'),
            'IMP' => array('Isle of Man Pound', '£'),
            'ILS' => array('Israel Shekel', '₪'),
            'JMD' => array('Jamaica Dollar', 'J$'),
            'JPY' => array('Japan Yen', '¥'),
            'JEP' => array('Jersey Pound', '£'),
            'KZT' => array('Kazakhstan Tenge', 'лв'),
            'KPW' => array('Korea (North) Won', '₩'),
            'KRW' => array('Korea (South) Won', '₩'),
            'KGS' => array('Kyrgyzstan Som', 'лв'),
            'LAK' => array('Laos Kip', '₭'),
            'LVL' => array('Latvia Lat', 'Ls'),
            'LBP' => array('Lebanon Pound', '£'),
            'LRD' => array('Liberia Dollar', '$'),
            'LTL' => array('Lithuania Litas', 'Lt'),
            'MKD' => array('Macedonia Denar', 'ден'),
            'MYR' => array('Malaysia Ringgit', 'RM'),
            'MUR' => array('Mauritius Rupee', '₨'),
            'MXN' => array('Mexico Peso', '$'),
            'MNT' => array('Mongolia Tughrik', '₮'),
            'MZN' => array('Mozambique Metical', 'MT'),
            'NAD' => array('Namibia Dollar', '$'),
            'NPR' => array('Nepal Rupee', '₨'),
            'ANG' => array('Netherlands Antilles Guilder', 'ƒ'),
            'NZD' => array('New Zealand Dollar', '$'),
            'NIO' => array('Nicaragua Cordoba', 'C$'),
            'NGN' => array('Nigeria Naira', '₦'),
            'KPW' => array('Korea (North) Won', '₩'),
            'NOK' => array('Norway Krone', 'kr'),
            'OMR' => array('Oman Rial', '﷼'),
            'PKR' => array('Pakistan Rupee', '₨'),
            'PAB' => array('Panama Balboa', 'B/.'),
            'PYG' => array('Paraguay Guarani', 'Gs'),
            'PEN' => array('Peru Nuevo Sol', 'S/.'),
            'PHP' => array('Philippines Peso', '₱'),
            'PLN' => array('Poland Zloty', 'zł'),
            'QAR' => array('Qatar Riyal', '﷼'),
            'RON' => array('Romania New Leu', 'lei'),
            'RUB' => array('Russia Ruble', 'руб'),
            'SHP' => array('Saint Helena Pound', '£'),
            'SAR' => array('Saudi Arabia Riyal', '﷼'),
            'RSD' => array('Serbia Dinar', 'Дин.'),
            'SCR' => array('Seychelles Rupee', '₨'),
            'SGD' => array('Singapore Dollar', '$'),
            'SBD' => array('Solomon Islands Dollar', '$'),
            'SOS' => array('Somalia Shilling', 'S'),
            'ZAR' => array('South Africa Rand', 'R'),
            'KRW' => array('Korea (South) Won', '₩'),
            'LKR' => array('Sri Lanka Rupee', '₨'),
            'SEK' => array('Sweden Krona', 'kr'),
            'CHF' => array('Switzerland Franc', 'CHF'),
            'SRD' => array('Suriname Dollar', '$'),
            'SYP' => array('Syria Pound', '£'),
            'TWD' => array('Taiwan New Dollar', 'NT$'),
            'THB' => array('Thailand Baht', '฿'),
            'TTD' => array('Trinidad and Tobago Dollar', 'TT$'),
            'TRL' => array('Turkey Lira', '₤'),
            'TVD' => array('Tuvalu Dollar', '$'),
            'UAH' => array('Ukraine Hryvnia', '₴'),
            'GBP' => array('United Kingdom Pound', '£'),
            'USD' => array('United States Dollar', '$'),
            'UYU' => array('Uruguay Peso', '$U'),
            'UZS' => array('Uzbekistan Som', 'лв'),
            'VEF' => array('Venezuela Bolivar', 'Bs'),
            'VND' => array('Viet Nam Dong', '₫'),
            'YER' => array('Yemen Rial', '﷼'),
            'ZWD' => array('Zimbabwe Dollar', 'Z$')
        );
    }

    public function to_currency_symbol($currency_code = 'USD', $locale = 'en_US')
    {
        $iso4217 = $this->iso4217();
        return isset($iso4217[$currency_code][1]) ? $iso4217[$currency_code][1] : '$';
    }

    public function iso4217_dropdown()
    {
        $iso4217 = $this->iso4217();
        $dropdown = array();
        foreach ($iso4217 as $key => $row) {
            $dropdown[$key] = $row[0];
        }
        return $dropdown;
    }

}
