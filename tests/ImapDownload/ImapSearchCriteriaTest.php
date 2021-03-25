<?php declare(strict_types=1);

namespace Granam\Tests\Mail\Download;

use Granam\Mail\Download\ImapSearchCriteria;
use PHPUnit\Framework\TestCase;

class ImapSearchCriteriaTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_create_it_with_any_search_charset()
    {
        $imapSearchCriteria = new ImapSearchCriteria();
        self::assertSame('UTF-8', $imapSearchCriteria->getCharsetForSearch());
        $imapSearchCriteria = new ImapSearchCriteria('ISO-8859-2');
        self::assertSame('ISO-8859-2', $imapSearchCriteria->getCharsetForSearch());
    }

    /**
     * @test
     */
    public function I_can_chain_setters()
    {
        $reflectionClass = new \ReflectionClass(ImapSearchCriteria::class);
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (strpos($method->getShortName(), 'is') === 0 || strpos($method->getShortName(), 'get') === 0 || strpos($method->getShortName(), '__') === 0) {
                continue;
            }
            self::assertTrue($method->hasReturnType(), "Method {$method->getName()} should have a return type");
            $returnType = $method->getReturnType();
            self::assertSame(ImapSearchCriteria::class, $returnType->getName());
        }
    }

    /**
     * @test
     */
    public function I_will_get_all_as_filter_when_nothing_set()
    {
        $imapSearchCriteria = new ImapSearchCriteria();
        self::assertSame('ALL', $imapSearchCriteria->getAsString());
        self::assertSame('ALL', (string)$imapSearchCriteria);
        $imapSearchCriteria->fetchAll();
        self::assertSame('ALL', $imapSearchCriteria->getAsString());
        self::assertSame('ALL', (string)$imapSearchCriteria);
    }

    /**
     * @test
     */
    public function I_can_get_filter_every_possible_combination()
    {
        $imapSearchCriteria = new ImapSearchCriteria();
        $imapSearchCriteria->filterAnswered();
        self::assertTrue($imapSearchCriteria->isAnswered());
        self::assertSame('ANSWERED', $imapSearchCriteria->getAsString());
        $imapSearchCriteria->filterByDate($now = new \DateTime());
        self::assertSame($now, $imapSearchCriteria->getByDate());
        self::assertSame('ANSWERED ON "' . $now->format('j F Y') . '"', $imapSearchCriteria->getAsString());
        $imapSearchCriteria->filterDeleted();
        self::assertTrue($imapSearchCriteria->isDeleted());
        self::assertSame('ANSWERED DELETED ON "' . $now->format('j F Y') . '"', $imapSearchCriteria->getAsString());
        $imapSearchCriteria->filterFlagged();
        self::assertTrue($imapSearchCriteria->isImportant());
        self::assertSame('ANSWERED DELETED FLAGGED ON "' . $now->format('j F Y') . '"', $imapSearchCriteria->getAsString());
        $imapSearchCriteria->filterKeywordContains('foo');
        self::assertSame('foo', $imapSearchCriteria->getKeywordContains());
        self::assertSame('ANSWERED DELETED FLAGGED KEYWORD "foo" ON "' . $now->format('j F Y') . '"', $imapSearchCriteria->getAsString());
        $imapSearchCriteria->filterKeywordNotContains('bar');
        self::assertSame('bar', $imapSearchCriteria->getKeywordNotContains());
        self::assertSame('ANSWERED DELETED FLAGGED KEYWORD "foo" UNKEYWORD "bar" ON "' . $now->format('j F Y') . '"', $imapSearchCriteria->getAsString());
        $imapSearchCriteria->filterNew();
        self::assertTrue($imapSearchCriteria->isNew());
        self::assertSame('ANSWERED DELETED FLAGGED KEYWORD "foo" UNKEYWORD "bar" NEW ON "' . $now->format('j F Y') . '"', $imapSearchCriteria->getAsString());
    }
}
