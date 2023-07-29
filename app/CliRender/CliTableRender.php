<?php

namespace App\CliRender;

class CliTableRender
{
    private array $items;
    private array $maxColumnContentLength;
    private int $columnSeparatorsLength;
    private int $columns;
    private int $tableLength;

    private string $columnCountErrorMessagePattern = 'Кол-во столбцов должно быть равное %s.';

    public function __construct(int $columnsCount, array $titleRow = [])
    {
        $titleRowCount = count($titleRow);
        if ($titleRowCount && $titleRowCount !== $columnsCount) throw new \Exception(sprintf($this->columnCountErrorMessagePattern, $columnsCount));

        $this->titleRow = $titleRow;
        $this->items = [];
        $this->maxColumnContentLength = [];
        $this->columns = $columnsCount;
        $this->columnSeparatorsLength = $columnsCount + 1;
        $this->tableLength = $this->columnSeparatorsLength;

        if ($titleRowCount) {
            $this->calculateMaxColumnsLength($this->titleRow);
        }
    }

    public function add(array $row): void
    {
        if (count($row) !== $this->columns) throw new \Exception(sprintf('Кол-во столбцов должно быть равное %s.', $this->columns));

        $this->items[] = $row;
        $this->calculateMaxColumnsLength($row);
    }

    public function render(): string
    {
        $result = '';

        foreach ($this->items as $row) {
            $result .= $this->renderRow($row);
            $result .= $this->renderRowSeparator();
        }

        if (count($this->titleRow)) {
            $rowTitle = $this->renderRow($this->titleRow);
            $result = $rowTitle . $this->renderRowSeparator() . $result;
            $result .= $rowTitle;
            $result .= $this->renderRowSeparator();
        }

        $result = $this->renderRowSeparator() . $result;

        return $result;
    }

    private function renderRow(array $row): string
    {
        $result = '';

        $paddingContent = ' ';
        $paddingLength = 1;
        $leftPaddingContent = str_repeat($paddingContent, $paddingLength);
        $rightPaddingContent = str_repeat($paddingContent, $paddingLength);
        $rowLength = $this->columnSeparatorsLength;
        $columnIndex = 0;
        $rowFormattedItems = [];
        foreach ($row as $content) {
            $contentLength = strlen($content);
            $rightPaddingLength = $this->maxColumnContentLength[$columnIndex] - $contentLength;
            if ($rightPaddingLength < 0) $rightPaddingLength = 0;

            $rowFormattedItems[] = vsprintf('%s%s%s%s', [
                $leftPaddingContent,
                $content,
                str_repeat($paddingContent, $rightPaddingLength),
                $rightPaddingContent,
            ]);

            $newRowLength = $rowLength + $paddingLength * 2 + $rightPaddingLength + $contentLength;
            if ($newRowLength > $rowLength) {
                $rowLength = $newRowLength;
            }

            ++$columnIndex;
        }
        $result .= sprintf('|%s|', implode('|', $rowFormattedItems)) . PHP_EOL;

        $this->tableLength = $rowLength;

        return $result;
    }

    private function renderRowSeparator(): string
    {
        return str_repeat('-', $this->tableLength) . PHP_EOL;
    }

    private function calculateMaxColumnsLength(array $row): void
    {
        $index = 0;
        foreach ($row as $content) {
            if (!isset($this->maxColumnContentLength[$index])) $this->maxColumnContentLength[$index] = 0;

            $contentLength = strlen($content);
            if ($contentLength > $this->maxColumnContentLength[$index]) {
                $this->maxColumnContentLength[$index] = $contentLength;
            }
            ++$index;
        }
    }
}