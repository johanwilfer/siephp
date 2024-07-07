<?php

namespace Tests\SIE\Dumper;

use PHPUnit\Framework\TestCase;
use SIE\Data\Account;
use SIE\Data\Company;
use SIE\Data\Transaction;
use SIE\Data\Verification;
use SIE\Data\VerificationSeries;
use SIE\Dumper\SIEDumper;

class SIEDumperTest extends TestCase
{
    public function testSimpleExample()
    {
        // create a company
        $company = (new Company())
            // set company name
            ->setCompanyName('My company')
            // add a verification series
            ->addVerificationSeries(new VerificationSeries())
            // add two accounts
            ->addAccount((new Account(1511))->setName('Kundfordringar'))
            ->addAccount((new Account(3741))->setName('Öresutjämning'))
        ;

        // add a verification with two transactions
        $verification = (new Verification(591000490))->setDate('20150105')
            ->addTransaction(
                (new Transaction())
                    ->setAccount($company->getAccount(1511))
                    ->setAmount(-0.24)
            )
            ->addTransaction(
                (new Transaction())
                    ->setAccount($company->getAccount(3741))
                    ->setAmount(0.24)
            )
        ;
        // add the verification to the company
        $company->getVerificationSeriesAll()[0]->addVerification($verification);
        // validate data, will throw Exception if invalid data
        $company->validate();

        // FIXME! Set fixed date (private) for generated at
        // FIXME! The encoding is tricky in tests, how do we handle that in a good way?

        $dumper = new SIEDumper();
        $output = $dumper->dump($company);

        $expected = <<<EOT
            #FLAGGA 0\r
            #FORMAT PC8\r
            #SIETYP 4\r
            #PROGRAM "SIE-PHP exporter" 1.0\r
            #GEN 20150921\r
            #FNAMN "My company"\r
            #KONTO 1511 Kundfordringar\r
            #KONTO 3741 �resutj�mning\r
            \r
            #VER A 591000490 20150105\r
            {\r
                #TRANS 1511 {} -0.24 20150105\r
                #TRANS 3741 {} 0.24 20150105\r
            }\r
            EOT;

        $this->assertEquals($expected, $output);
    }
}