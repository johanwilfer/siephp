<?php

declare(strict_types=1);

use SIE\Data\Account;
use SIE\Data\Company;
use SIE\Data\Transaction;
use SIE\Data\Verification;
use SIE\Data\VerificationSeries;
use SIE\Dumper\SIEDumper;

/**
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
$company = (new Company())
    // set company name
    ->setCompanyName('My company')
    // add a verification series
    ->addVerificationSeries(new VerificationSeries())
;

// add two accounts
$account1511 = (new Account(1511))->setName('Kundfordringar');
$account3741 = (new Account(3741))->setName('Öresutjämning');
$company
    ->addAccount($account1511)
    ->addAccount($account3741)
;

// add a verification with two transactions
$verification = (new Verification('591000490'))->setDate('20150105')
    ->addTransaction(
        (new Transaction())
            ->setAccount($account1511)
            ->setAmount(-0.24)
    )
    ->addTransaction(
        (new Transaction())
            ->setAccount($account3741)
            ->setAmount(0.24)
    )
;
// add the verification to the company
$company->getVerificationSeriesAll()[0]->addVerification($verification);
// validate data, will throw Exception if invalid data
$company->validate();

$dumper = new SIEDumper();
$output = $dumper->dump($company);
echo $output;
