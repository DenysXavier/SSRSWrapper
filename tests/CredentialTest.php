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

use DenysXavier\SSRSWrapper\Credential;
use Faker\{Factory, Generator};
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Report class
 */
class CredentialTest extends TestCase
{
    private static Generator $faker;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$faker = Factory::create("pt_BR");
    }

    /**
     * @test
     */
    public function whenNoDomainIsSetUSERPWDIsComposedByUsernameAndPasswordSeparatedByColon()
    {
        $username = self::$faker->userName;
        $password = self::$faker->password;
        $expected = $username . ':' . $password;

        $credential = new Credential($username, $password);

        $this->assertEquals($expected, $credential->buildUSERPWD());
    }

    /**
     * @test
     */
    public function USERPWDIsComposedByDomainAndUsernameSeparatedBySlashAndPasswordSeparatedByColon()
    {
        $username = self::$faker->userName;
        $password = self::$faker->password;
        $domain = self::$faker->domainName;
        $expected = $domain . '/' . $username . ':' . $password;

        $credential = new Credential($username, $password, $domain);

        $this->assertEquals($expected, $credential->buildUSERPWD());
    }
}
