<?php
/*
 * Copyright 2020 Benjamin Kleiner
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies
 * or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO
 * THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR
 * THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOPgSql\Driver as PostgresDriver;
use Doctrine\ORM\QueryBuilder;

trait ContainsFilterTrait
{

    /**
     * @param QueryBuilder                $queryBuilder
     * @param                             $value
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string                      $property
     */
    private function buildContainsQueries(QueryBuilder $queryBuilder, $value, QueryNameGeneratorInterface $queryNameGenerator, string $property): void
    {
        if ($queryBuilder->getEntityManager()->getConnection()->getDriver() instanceof PostgresDriver) {
            $groups = array_merge(['all' => [], 'some' => [], 'none' => [], 'not' => []], $value);
            if ([] !== $groups['all']) {
                $parameterName = $queryNameGenerator->generateParameterName($property);
                $queryBuilder
                    ->andWhere("JSON_CONTAINS_ALL(o.{$property}, :{$parameterName}) = TRUE")
                    ->setParameter($parameterName, $groups['all'], Connection::PARAM_STR_ARRAY)
                ;
            }
            if ([] !== $groups['some']) {
                $parameterName = $queryNameGenerator->generateParameterName($property);
                $queryBuilder
                    ->andWhere("JSON_CONTAINS_ANY(o.{$property}, :{$parameterName}) = TRUE")
                    ->setParameter($parameterName, $groups['some'], Connection::PARAM_STR_ARRAY)
                ;
            }
            if ([] !== $groups['none']) {
                $parameterName = $queryNameGenerator->generateParameterName($property);
                $queryBuilder
                    ->andWhere("JSON_CONTAINS_ANY(o.{$property}, :{$parameterName}) = FALSE")
                    ->setParameter($parameterName, $groups['none'], Connection::PARAM_STR_ARRAY)
                ;
            }
            if ([] !== $groups['not']) {
                $parameterName = $queryNameGenerator->generateParameterName($property);
                $queryBuilder
                    ->andWhere("JSON_CONTAINS_ALL(o.{$property}, :{$parameterName}) = FALSE")
                    ->setParameter($parameterName, $groups['not'], Connection::PARAM_STR_ARRAY)
                ;
            }
        }
    }
}