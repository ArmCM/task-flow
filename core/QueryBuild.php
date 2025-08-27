<?php

namespace Core;

class QueryBuild
{
    private string $table;
    private array $conditions = [];
    private array $params = [];
    private string $orderBy = '';
    private ?int $limit = null;
    private ?int $offset = null;

    public function __construct(private Database $database, string $table)
    {
        $this->table = $table;
    }

    public function where(string $condition, array $params = []): self
    {
        $this->conditions[] = $condition;

        $this->params = array_merge($this->params, $params);

        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy = "ORDER BY {$column} {$direction}";

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    public function getAll(): array
    {
        $where = $this->conditions ? "WHERE " . implode(" AND ", $this->conditions) : '';

        $sql = "SELECT * FROM {$this->table} {$where} {$this->orderBy}";

        if (notEmpty($this->limit)) {
            $sql .= " LIMIT {$this->limit}";
        }

        if (notEmpty($this->offset)) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $this->database->query($sql, $this->params)->fetchAll();
    }

    public function get()
    {
        $where = $this->conditions ? "WHERE " . implode(" AND ", $this->conditions) : '';

        $sql = "SELECT * FROM {$this->table} {$where} {$this->orderBy}";

        return $this->database->query($sql, $this->params)->fetch();
    }

    public function delete()
    {
        $where = $this->conditions ? "WHERE " . implode(" AND ", $this->conditions) : '';

        $sql = "DELETE FROM {$this->table} {$where}";

        return $this->database->query($sql, $this->params)->fetch();
    }

    public function update($params, array $values)
    {
        $where = $this->conditions ? "WHERE " . implode(" AND ", $this->conditions) : '';

        return $this->database->query("UPDATE {$this->table} SET $params {$where}", $values)->fetch();
    }

    public function store($params, $data): Database
    {
        $placeholders = ':' . implode(', :', array_map('trim', explode(',', $params)));

        return $this->database->query("INSERT INTO {$this->table}($params) VALUES ($placeholders)", $data);
    }

    public function count(): int
    {
        $totalStmt = $this->database->query("SELECT COUNT(*) as total FROM {$this->table}");

        return $totalStmt->fetch()['total'];
    }

    public function paginate(int $page = 1, int $perPage = 3): array
    {
        $total = $this->count();
        $lastPage = (int) ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        $this->limit($perPage)->offset($offset);

        $data = $this->getAll();

        return [
            'data' => $data,
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
            ],
            'links' => [
                'first' => "/tasks?page=1&per_page={$perPage}",
                'last' => "/tasks?page={$lastPage}&per_page={$perPage}",
                'prev' => $page > 1 ? "/tasks?page=" . ($page - 1) . "&per_page={$perPage}" : null,
                'next' => $page < $lastPage ? "/tasks?page=" . ($page + 1) . "&per_page={$perPage}" : null,
            ]
        ];
    }
}
