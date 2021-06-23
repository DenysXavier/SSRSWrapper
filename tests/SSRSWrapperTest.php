<?php

/**
 * Copyright 2021 Denys Xavier
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace DenysXavier\SSRSWrapper\Tests;

use DenysXavier\SSRSWrapper\{Report, SaveOnDisk, SSRSWrapper};
use Exception;
use Faker\{Factory, Generator};
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for SSRSWrapper class
 */
class SSRSWrapperTest extends TestCase
{
    private static Generator $faker;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$faker = Factory::create();
    }

    /**
     * @test
     */
    public function theDefaultValueOfVirtualDirectoryIsReportServer()
    {
        $ssrs = new SSRSWrapper("http://fake-host");

        $this->assertEquals("ReportServer", $ssrs->getVirtualDirectory());
    }

    /**
     * @test
     */
    public function URLisBuiltAsHostAndVirtualDirectoryAndReportPath()
    {
        $host = 'http://' . self::$faker->domainName;
        $virtualDirectory = self::$faker->word;
        $reportPath = self::$faker->regexify('[a-z0-9]{5}/[a-z0-9]{5}');

        $expected = $host . '/' . $virtualDirectory . '?' . urlencode($reportPath);

        $report = new Report($reportPath);
        $ssrs = new SSRSWrapper($host, $virtualDirectory);

        $actual = $ssrs->buildURL($report);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function AnyReportParameterMustBeAddedToTheURLAsQueryString()
    {
        $host = 'http://' . self::$faker->domainName;
        $virtualDirectory = self::$faker->word;
        $reportPath = self::$faker->regexify('[a-z0-9]{5}/[a-z0-9]{5}');
        $reportParameters = [
            self::$faker->word => self::$faker->word,
            self::$faker->word => self::$faker->randomNumber()
        ];

        $expected = $host . '/' . $virtualDirectory . '?' . urlencode($reportPath) . "&" . http_build_query($reportParameters);

        $report = new Report($reportPath);
        foreach ($reportParameters as $param => $value) {
            $report->addParam($param, $value);
        }

        $ssrs = new SSRSWrapper($host, $virtualDirectory);

        $actual = $ssrs->buildURL($report);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function URLmayBeBuiltWithExtraParameters()
    {
        $host = 'http://' . self::$faker->domainName;
        $virtualDirectory = self::$faker->word;
        $reportPath = self::$faker->regexify('[a-z0-9]{5}/[a-z0-9]{5}');
        $reportParameters = [
            self::$faker->word => self::$faker->word,
            self::$faker->word => self::$faker->randomNumber()
        ];
        $extraParameters = [
            self::$faker->word => self::$faker->word
        ];
        $parameters = array_merge($reportParameters, $extraParameters);

        $expected = $host . '/' . $virtualDirectory . '?' . urlencode($reportPath) . "&" . http_build_query($parameters);

        $report = new Report($reportPath);
        foreach ($reportParameters as $param => $value) {
            $report->addParam($param, $value);
        }

        $ssrs = new SSRSWrapper($host, $virtualDirectory);

        $actual = $ssrs->buildURL($report, $extraParameters);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function anExceptionIsThrownIfAnErrorOccursDuringCURLRequest()
    {
        $ssrs = new SSRSWrapper("http://fake-host");
        $report = new Report(self::$faker->regexify('[a-z0-9]{5}/[a-z0-9]{5}'));
        $behavior = new SaveOnDisk('php://temp');

        $this->expectException(Exception::class);

        $ssrs->export($report, $behavior);
    }
}
