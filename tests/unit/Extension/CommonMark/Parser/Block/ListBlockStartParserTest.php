<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension\CommonMark\Parser\Block;

use League\CommonMark\Configuration\Configuration;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Extension\CommonMark\Parser\Block\ListBlockStartParser;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;
use PHPUnit\Framework\TestCase;

final class ListBlockStartParserTest extends TestCase
{
    public function testOrderedListStartingAtOne()
    {
        $cursor = new Cursor('1. Foo');

        $parser = new ListBlockStartParser();
        $start = $parser->tryStart($cursor, $this->createMock(MarkdownParserStateInterface::class));

        $this->assertNotNull($start);

        $parsers = $start->getBlockParsers();
        $this->assertCount(2, $parsers);

        /** @var ListBlock $block */
        $block = $parsers[0]->getBlock();
        $this->assertInstanceOf(ListBlock::class, $block);

        /** @var ListItem $item */
        $item = $parsers[1]->getBlock();
        $this->assertInstanceOf(ListItem::class, $item);

        $this->assertSame(ListBlock::TYPE_ORDERED, $block->getListData()->type);
        $this->assertSame(1, $block->getListData()->start);

        $this->assertSame(ListBlock::TYPE_ORDERED, $item->getListData()->type);
    }

    public function testOrderedListStartingAtTwo()
    {
        $cursor = new Cursor('2. Foo');

        $parser = new ListBlockStartParser();
        $start = $parser->tryStart($cursor, $this->createMock(MarkdownParserStateInterface::class));

        $this->assertNotNull($start);

        $parsers = $start->getBlockParsers();
        $this->assertCount(2, $parsers);

        /** @var ListBlock $block */
        $block = $parsers[0]->getBlock();
        $this->assertInstanceOf(ListBlock::class, $block);

        /** @var ListItem $item */
        $item = $parsers[1]->getBlock();
        $this->assertInstanceOf(ListItem::class, $item);

        $this->assertSame(ListBlock::TYPE_ORDERED, $block->getListData()->type);
        $this->assertSame(2, $block->getListData()->start);

        $this->assertSame(ListBlock::TYPE_ORDERED, $item->getListData()->type);
    }

    public function testUnorderedListWithDashMarker()
    {
        $cursor = new Cursor('- Foo');

        $parser = new ListBlockStartParser();
        $start = $parser->tryStart($cursor, $this->createMock(MarkdownParserStateInterface::class));

        $this->assertNotNull($start);

        $parsers = $start->getBlockParsers();
        $this->assertCount(2, $parsers);

        /** @var ListBlock $block */
        $block = $parsers[0]->getBlock();
        $this->assertInstanceOf(ListBlock::class, $block);

        /** @var ListItem $item */
        $item = $parsers[1]->getBlock();
        $this->assertInstanceOf(ListItem::class, $item);

        $this->assertSame(ListBlock::TYPE_BULLET, $block->getListData()->type);
        $this->assertSame('-', $block->getListData()->bulletChar);

        $this->assertSame(ListBlock::TYPE_BULLET, $item->getListData()->type);
    }

    public function testUnorderedListWithAsteriskMarker()
    {
        $cursor = new Cursor('* Foo');

        $parser = new ListBlockStartParser();
        $start = $parser->tryStart($cursor, $this->createMock(MarkdownParserStateInterface::class));

        $this->assertNotNull($start);

        $parsers = $start->getBlockParsers();
        $this->assertCount(2, $parsers);

        /** @var ListBlock $block */
        $block = $parsers[0]->getBlock();
        $this->assertInstanceOf(ListBlock::class, $block);

        /** @var ListItem $item */
        $item = $parsers[1]->getBlock();
        $this->assertInstanceOf(ListItem::class, $item);

        $this->assertSame(ListBlock::TYPE_BULLET, $block->getListData()->type);
        $this->assertSame('*', $block->getListData()->bulletChar);

        $this->assertSame(ListBlock::TYPE_BULLET, $item->getListData()->type);
    }

    public function testUnorderedListWithPlusMarker()
    {
        $cursor = new Cursor('+ Foo');

        $parser = new ListBlockStartParser();
        $start = $parser->tryStart($cursor, $this->createMock(MarkdownParserStateInterface::class));

        $this->assertNotNull($start);

        $parsers = $start->getBlockParsers();
        $this->assertCount(2, $parsers);

        /** @var ListBlock $block */
        $block = $parsers[0]->getBlock();
        $this->assertInstanceOf(ListBlock::class, $block);

        /** @var ListItem $item */
        $item = $parsers[1]->getBlock();
        $this->assertInstanceOf(ListItem::class, $item);

        $this->assertSame(ListBlock::TYPE_BULLET, $block->getListData()->type);
        $this->assertSame('+', $block->getListData()->bulletChar);

        $this->assertSame(ListBlock::TYPE_BULLET, $item->getListData()->type);
    }

    public function testUnorderedListWithCustomMarker()
    {
        $cursor = new Cursor('^ Foo');

        $parser = new ListBlockStartParser();
        $parser->setConfiguration(new Configuration(['unordered_list_markers' => ['^']]));
        $start = $parser->tryStart($cursor, $this->createMock(MarkdownParserStateInterface::class));

        $this->assertNotNull($start);

        $parsers = $start->getBlockParsers();
        $this->assertCount(2, $parsers);

        /** @var ListBlock $block */
        $block = $parsers[0]->getBlock();
        $this->assertInstanceOf(ListBlock::class, $block);

        /** @var ListItem $item */
        $item = $parsers[1]->getBlock();
        $this->assertInstanceOf(ListItem::class, $item);

        $this->assertSame(ListBlock::TYPE_BULLET, $block->getListData()->type);
        $this->assertSame('^', $block->getListData()->bulletChar);

        $this->assertSame(ListBlock::TYPE_BULLET, $item->getListData()->type);
    }

    public function testUnorderedListWithDisabledMarker()
    {
        $cursor = new Cursor('+ Foo');

        $parser = new ListBlockStartParser();
        $parser->setConfiguration(new Configuration(['unordered_list_markers' => ['-', '*']]));
        $start = $parser->tryStart($cursor, $this->createMock(MarkdownParserStateInterface::class));

        $this->assertNull($start);
    }

    public function testInvalidListMarkerConfiguration()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid configuration option "unordered_list_markers": value must be an array of strings');

        $cursor = new Cursor('- Foo');

        $parser = new ListBlockStartParser();
        $parser->setConfiguration(new Configuration(['unordered_list_markers' => '-']));
        $parser->tryStart($cursor, $this->createMock(MarkdownParserStateInterface::class));
    }
}
