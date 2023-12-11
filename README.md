# SIEPHP

## About
SIEPHP is a library that enables export of bookkeeping-data to the Swedish SIE-format. For more information about SIE see http://www.sie.se/

Currently only a subset of the specification is supported, like export to SIE4 (verification/transaction data). In the future a loader-class for SIE could be written as well, and support of more features in the SIE-standard.

It's built around simple data-classes that represents a Company / Verifications / Transactions by the model in the SIE-standard. It also comes with a dumper-class that can dump the data to SIE-format.

## Installation
Install the latest version with

```bash
$ composer require jttech/sie
```

## Usage

[examples/](examples) holds some examples on how to use SIE-PHP. This is [examples/simple.php](examples/simple.php):
```php
// create a company
$company = (new SIE\Data\Company())
    // set company name
    ->setCompanyName('My company')
    // add a verification series
    ->addVerificationSeries(new SIE\Data\VerificationSeries())
    // add two accounts
    ->addAccount((new SIE\Data\Account(1511))->setName('Kundfordringar'))
    ->addAccount((new SIE\Data\Account(3741))->setName('Öresutjämning'))
;

// add a verification with two transactions
$verification = (new SIE\Data\Verification(591000490))->setDate('20150105')
    ->addTransaction(
        (new SIE\Data\Transaction())
            ->setAccount($company->getAccount(1511))
            ->setAmount(-0.24)
    )
    ->addTransaction(
        (new SIE\Data\Transaction())
            ->setAccount($company->getAccount(3741))
            ->setAmount(0.24)
    )
;
// add the verification to the company
$company->getVerificationSeriesAll()[0]->addVerification($verification);
// validate data, will throw Exception if invalid data
$company->validate();

$dumper = new SIE\Dumper\SIEDumper();
$output = $dumper->dump($company);
echo $output;
```

And it will generate the following output (in PC8/CP437 encoding):
```
#FLAGGA 0
#FORMAT PC8
#SIETYP 4
#PROGRAM "SIE-PHP exporter" 1.0
#GEN 20150921
#FNAMN "My company"
#KONTO 1511 Kundfordringar
#KONTO 3741 Öresutjämning

#VER A 591000490 20150105
{
    #TRANS 1511 {} -0.24 20150105
    #TRANS 3741 {} 0.24 20150105
}
```

See [examples/TSVtoSIE.php](examples/TSVtoSIE.php) for a custom TSV to SIE converter, loading the same data from a TSV-file.

## Author
Johan Wilfer - johan [at] jttech.se - http://jttech.se

## Licence
SIE-PHP is licensed under the MIT License - see the LICENSE file for details

## Thanks to
Mitronic AB (http://mitronic.se) that funded this work.

## Questions?
Send an email to johan [at] jttech.se
