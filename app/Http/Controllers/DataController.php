<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class DataController extends Controller
{
    public function index(Request $request) {
        // Define the conversion rates for each currency relative to USD
        $conversionRates = [
            'USD' => 1.0,
            'EUR' => 0.90,
            'DKK' => 6.75,
            'AUD' => 1.49,
            'CAD' => 1.36,
            'GBP' => 0.76,
            'SGD' => 1.31,
            'SEK' => 10.29,
            'BRL' => 5.61,
            'CHF' => 0.851747,
            'JPY' => 145.59,
            'MXN' => 19.94,
            'NZD' => 1.61,
            'PLN' => 3.87,
        ];
        
        // Read the CSV file from the public folder
        $file = fopen(public_path('data.csv'), 'r');
        
        // Create a new CSV file for writing matched entries
        $newCsvFile = fopen(storage_path('new_data.csv'), 'w');
        
        // Read the header row and map new headers
        $header = fgetcsv($file);
        $newHeader = [
            'SubscriptionId', 
            'RepricedUnitPrice', 
            'RepriceSubtotal', 
            'RepriceOverrideDiscount', 
            'Initial Currency', 
            'Initial Price'
        ];
        
        // Write the new header to the new CSV file
        fputcsv($newCsvFile, $newHeader);
        
        // Read the CSV file line by line
        while (($row = fgetcsv($file)) !== false) {
            $originalCurrency = $row[1];
            $originalPrice = (float) $row[2];
            $row[3]= '0.00';
            
            if ($row[1] == 'USD') {
                // If currency is USD, set UPCOMING_REBILL_TOTAL to 29.99
                $row[2] = 29.99;
            } else {
                // Convert the UPCOMING_REBILL_TOTAL to USD
                $row[2] = 29.99 * $conversionRates[$originalCurrency];
            }
    
            // Map and prepare the row data with new field names
            $newRow = [
                'SubscriptionId' => $row[0], // CLASSIC_SUBID -> SubscriptionId
                'RepricedUnitPrice' => $row[2], // UPCOMING_REBILL_TOTAL -> RepricedUnitPrice
                'RepriceSubtotal' => $row[2], // RepriceSubtotal is same as RepricedUnitPrice
                'RepriceOverrideDiscount' => $row[3], // UPCOMINGDISCOUNT -> RepriceOverrideDiscount
                'Initial Currency' => $originalCurrency, // Add Initial Currency
                'Initial Price' => $originalPrice, // Add Initial Price
            ];
    
            // Write the updated row to the new CSV file
            fputcsv($newCsvFile, $newRow);
        }
        
        // Close both files
        fclose($file);
        fclose($newCsvFile);
        
        return response()->download(storage_path('new_data.csv'));
    }
    

   
    
    
}
