<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MigrateCsv extends Command
{
    const ROW_DIVIDER = '/\R/';
    const COL_DIVIDER = ',';
    protected            $signature   = 'migrate:csv {--file=random.csv}';
    protected            $description = 'Миграция данных из csv файлов в таблицу customers';
    protected array      $originalRows;
    protected Collection $headRows;
    protected array      $contentRows;
    protected int        $countCols   = 0;
    protected array      $errors;


    public function handle()
    {
        $this->info('Начало выполнения команды:' . $this->description);
        $this->parseFile();
        $this->createCustomers();
    }

    public function createCustomers(): void
    {
        foreach ($this->contentRows as $item) {
            $this->createCustomerItem($item);
        }
    }

    public function createCustomerItem(Collection $item): void
    {
        $validator = Validator::make($item->toArray(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email:rfc,dns',
            'age'      => 'required|integer|min:18|max:99',
            'location' => 'string',
        ]);
        if ($validator->fails()) {
            $this->setError($validator->errors()->keys(), $item['id']);

            return;
        }

        Customer::create($validator->validated());
    }

    private function setError(array $keys, $id): void
    {
        $this->errors[] = [
            'column'    => implode(',', $keys),
            'originial' => $this->originalRows[$id],
        ];
    }

    public function parseFile(): void
    {
        $this->originalRows = $this->getRows(preg_replace("/\R$/", '', $this->getFileContent()));
        if (count($this->originalRows) < 2) {
            $this->error('Пустой файл');
            exit();
        }

        $this->headRows    = collect(explode(self::COL_DIVIDER, $this->originalRows[0]))->map(fn($i) => trim($i));
        $this->countCols   = $this->headRows->count();
        $this->contentRows = $this->getRowsColumns(array_slice($this->originalRows, 1));
    }

    public function getFileContent(): string
    {
        return Storage::disk('local')->get('resource' . DIRECTORY_SEPARATOR . $this->option('file'));
    }

    public function getRows(string $content): array
    {
        return array_map(fn($s) => trim($s, '"'), preg_split(self::ROW_DIVIDER, $content));
    }

    public function getRowsColumns(array $rows): array
    {
        $result = [];
        foreach ($rows as $item) {
            $result[] = $this->headRows->combine(
                array_pad($this->clearStrings(explode(self::COL_DIVIDER, $item)), $this->countCols, '')
            );
        }

        return $result;
    }

    public function clearStrings(array $cols): array
    {
        return array_map(function ($item) {
            return trim(trim($item, '"'));
        }, $cols);
    }
}
