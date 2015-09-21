<?php

/*
 * This file is part of the SIE-PHP package.
 *
 * (c) Johan Wilfer <johan@jttech.se>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Autoload files using Composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

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

