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

use DenysXavier\SSRSWrapper\Report;
use Faker\{Factory, Generator};
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Report class
 */
class ReportTest extends TestCase
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
    public function addParamsWithDifferentNamesCreatesADistinctCollection()
    {
        $expected = [
            "param1" => self::$faker->word(),
            "param2" => self::$faker->randomNumber(),
            "param3" => self::$faker->date()
        ];

        $report = new Report("/fake/path");

        foreach ($expected as $param => $value) {
            $report->addParam($param, $value);
        }

        $this->assertEquals($expected, $report->getParams());
    }

    /**
     * @test
     */
    public function addNewParamWithAnAlreadyUsedNameOverwritesOldValue()
    {
        $report = new Report("/fake/path");

        $oldValue = self::$faker->word;
        $newValue = self::$faker->randomNumber();

        $report->addParam("param", $oldValue);
        $this->assertEquals($oldValue, $report->getParam("param"));

        $report->addParam("param", $newValue);
        $this->assertNotEquals($oldValue, $report->getParam("param"));
        $this->assertEquals($newValue, $report->getParam("param"));

        $quantityOfItemsInTheCollection = count($report->getParams());
        $this->assertEquals(1, $quantityOfItemsInTheCollection);
    }
}
