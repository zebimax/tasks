<?php

namespace Ajax\Tasks\Core;

use Ajax\Tasks\Core\Exception\DBException;

abstract class DBTable
{
    const SORT_DESC = 'desc';
    const SORT_ASC = 'asc';
    protected $db;
    protected $insertSql = 'INSERT INTO %s (%s) VALUES (%s);';
    protected $updateSql = 'UPDATE %s SET %s WHERE %s';

    /**
     * @param DB $db
     */
    public function __construct(DB $db)
    {
        $this->db = $db;
    }

    /**
     * @return array
     */
    abstract protected function getColumns();

    /**
     * @return string
     */
    abstract protected function getTableName();

    /**
     * @return array
     * @throws DBException
     */
    public function getData($page, $perPage, $sort = '')
    {
        $limitStr = $sortParam = '';
        $params = [];
        $orderBySql = '';

        if ($sort) {
            list($sortField, $sortDirection) = explode('.', $sort);
            if (!in_array($sortDirection, [self::SORT_ASC, self::SORT_DESC], true)) {
                $sortField = sprintf('%s=?', $sortField);
                $sortParam = $sortDirection;
                $sortDirection = strtoupper(self::SORT_DESC);
            } else {
                $sortDirection = strtoupper($sortDirection);
            }
            $orderBySql = sprintf(' ORDER BY %s %s', $sortField, $sortDirection);
        }

        $start = ($page * $perPage) - $perPage;
        if ($perPage) {
            $limitStr = sprintf(' LIMIT %s, %s', $start, $perPage);
        }
        $query      = $this->getSelectSql($orderBySql, $limitStr);
        $countQuery = sprintf('SELECT count(*) AS total FROM (%s) AS tq', $this->getSelectSql());
        $stmt = $this->db->getConnection()->prepare($query);
        $countStmt = $this->db->getConnection()->prepare($countQuery);
        if ($sortParam) {
            $stmt->bindValue(1, $sortParam);
            $countStmt->bindValue(1, $sortParam);
        }
        if (!$stmt->execute()) {
            throw new DBException('Invalid query: ' . $query . '. Error: ' . implode(PHP_EOL, $stmt->errorInfo()));
        }
        if (!$countStmt->execute()) {
            throw new DBException(
                'invalid query: ' . $countQuery. '. Error: ' . implode(PHP_EOL, $countStmt->errorInfo())
            );
        }
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $countStmt->execute($params);
        $countResult = $countStmt->fetch(\PDO::FETCH_ASSOC);

        return ['count' => (int) $countResult['total'], 'data' => $result];
    }

    /**
     * @param int $id
     *
     * @return array|false
     * @throws DBException
     */
    public function getById($id)
    {
        $query      = $this->getSelectSql() . 'WHERE id = ?';
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindValue(1, $id);
        if (!$stmt->execute()) {
            throw new DBException('invalid query: ' . implode(':', $stmt->errorInfo()));
        }

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result;
    }

    /**
     * @param array $data
     *
     * @return string
     * @throws DBException
     */
    public function insert(array $data)
    {
        $columnNames = $this->getColumns();
        $columnsSql = '';
        $values = [];
        foreach ($columnNames as $columnName) {
            $columnsSql .= sprintf('`%s`,', $columnName);
            $values[] = isset($data[$columnName]) ? $data[$columnName] : '';
        }
        $columnsSql = trim($columnsSql, ',');
        $sql = sprintf(
            $this->insertSql,
            $this->getTableName(),
            $columnsSql,
            implode(',', array_fill(0, count($values), '?'))
        );
        $stmt = $this->db->getConnection()->prepare($sql);
        foreach ($values as $key => $value) {
            $param = $key + 1;
            $stmt->bindValue($param, $value);
        }
        if (!$stmt->execute()) {
            throw new DBException('invalid query: ' . implode(':', $stmt->errorInfo()));
        }
        return $this->db->getConnection()->lastInsertId();
    }

    /**
     * @param int   $id
     * @param array $data
     *
     * @return bool
     * @throws DBException
     */
    public function update($id, array $data)
    {
        $columnNames = array_keys($data);
        $columnsSql = '';
        $values = [];
        foreach ($columnNames as $columnName) {
            $columnsSql .= sprintf('%s=?,', $columnName);
            $values[] = $data[$columnName];


        }
        $columnsSql = trim($columnsSql, ',');
        $sql = sprintf(
            $this->updateSql,
            $this->getTableName(),
            $columnsSql,
            'id = ?'
        );
        $stmt = $this->db->getConnection()->prepare($sql);
        $param = 1;
        foreach ($values as $key => $value) {
            $stmt->bindValue($param, $value);
            $param++;
        }
        $stmt->bindValue($param, $id);
        if (!$stmt->execute()) {
            throw new DBException('invalid query: ' . implode(':', $stmt->errorInfo()));
        }

        return true;
    }

    /**
     * @param string $orderBySql
     * @param string $limitStr
     *
     * @return string
     */
    protected function getSelectSql($orderBySql = '', $limitStr = '')
    {
        $query = sprintf('SELECT * FROM `%s`%s%s', $this->getTableName(), $orderBySql, $limitStr);

        return $query;
    }
}
