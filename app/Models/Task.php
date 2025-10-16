<?php

namespace App\Models;

use Carbon\Carbon;
use Core\App;
use Core\Database;
use Core\QueryBuild;

class Task
{
    protected Database $database;
    protected QueryBuild $query;

    public function __construct()
    {
        $this->query = new QueryBuild(App::resolve(Database::class), 'tasks');
    }

    public function all($filters = null, $sort = null): array
    {
        $this->applySearchFiltersToQuery($filters);

        $sort = $this->sort($sort);

        return $this->query->orderBy('created_at', $sort)->getAll();
    }

    public function paginate($filters, $sort): array
    {
        $this->applySearchFiltersToQuery($filters);

        $sort = $this->sort($sort);

        $page = empty($filters['page']) ? 1 : (int) $filters['page'];
        $perPage = empty($filters['per_page']) ? 10 : (int) $filters['per_page'];

        return $this->query->orderBy('created_at', $sort)->paginate($page, $perPage);
    }

    public function find(int $id): array|bool
    {
        return $this->query->where('id = :id', [':id' => $id])->get();
    }

    public function store(array $data): void
    {
        $this->query->store(
            'title, description, expiration_date, created_at',
            [
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':expiration_date' => Carbon::parse($data['expiration_date'])->format('Y-m-d H:i:s'),
                ':created_at' => Carbon::now()->toDateTimeString()
            ]
        );
    }

    public function update(int $id, array $data): void
    {
        $this->query->where("id = :id", [':id' => $id])->update(
            'title = :title,
                description = :description,
                status = :status,
                expiration_date = :expiration_date,
                updated_at = :updated_at',
            [
                ':id' => $id,
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':status' => $data['status'],
                ':expiration_date' => Carbon::parse($data['expiration_date'])->format('Y-m-d'),
                ':updated_at' => Carbon::now()->toDateTimeString()
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->query->where('id = :id', [':id' => $id])->delete();
    }

    protected function applySearchFiltersToQuery($filters): void
    {
        if (notEmpty($filters['title'])) {
            $this->query->where("title = :title", [':title' => $filters['title']]);
        }

        if (notEmpty($filters['status'])) {
            $this->query->where("status = :status", [':status' => $filters['status']]);
        }

        if (notEmpty($filters['expiration_date'])) {
            $this->query->where("DATE(expiration_date) = :expiration_date", [':expiration_date' => $filters['expiration_date']]);
        }
    }

    protected function sort($value): string
    {
        return notEmpty($value) && in_array(strtolower($value), ['asc', 'desc']) ? strtoupper($value) : 'DESC';
    }
}
