<?php

  /**
   * Query class
   */

  class Query extends Eloquent {

    /**
     * Select fields
     */
    protected $qFields = array('*');

    /**
     * Where
     */
    protected $qWheres = array();

    /**
     * Having
     */
    protected $qHavings = array();

    /**
     * Unions
     */
    protected $qUnions = array();

    /**
     * Froms
     */
    protected $qFrom = null;

    /**
     * From alias
     */
    protected $qFromAlias = null;

    /**
     * Limits
     */
    protected $qLimit = null;

    /**
     * Order
     */
    protected $qOrder = null;

    /**
     * Select
     */
    function qAddSelect($rawSelect) {
      // If not yet in fields
      if (!in_array($rawSelect, $this->qFields)) {
        // Add
        $this->qFields[] = $rawSelect;
      }
      // Return
      return $this;
    }
    /**
     * Select overwrite
     */
    function qSelect($rawSelect) {
      // Overwrite fields
      $this->qFields = array($rawSelect);
      // Return
      return $this;
    }

    /**
     * Where
     */
    function qWhere($rawCondition) {
      // Add to conditions
      $this->qWheres[] = $rawCondition;
      // Return
      return $this;
    }

    /**
     * Having
     */
    function qHaving($rawCondition) {
      // Add to havings
      $this->qHavings[] = $rawCondition;
      // Return
      return $this;
    }

    /**
     * Union
     */
    function qUnion(Query $query) {
      // Add to unions
      $this->qUnions[] = $query;
      // Return
      return $this;
    }

    /**
     * From
     */
    function qFrom(Query $query, $rawAlias) {
      // Add from
      $this->qFrom = $query;
      // Set alias
      $this->qFromAlias = $rawAlias;
      // Return
      return $this;
    }

    /**
     * Limit
     */
    function qLimit($start, $limit) {
      // Add limit
      $this->qLimit = $start . ', ' . $limit;
      // Return
      return $this;
    }

    /**
     * Order
     */
    function qOrder($rawField, $sort = 'asc') {
      // Add order
      $this->qOrder = $rawField . ' ' . $sort;
      // Return
      return $this;
    }

    /**
     * Randomize
     */
    function qRand() {
      // Set order
      $this->qOrder = 'rand()';
      // Return
      return $this;
    }

    /**
     * Sql
     */
    function qSql() {
      // Convert to sql
      $sql = array('select');
      // Set fields
      if (!$this->qFields) $this->qFields = array('*');
      // Add fields
      $sql[] = implode(', ', $this->qFields);

      // If there's from, get sql
      if ($this->qFrom) {
        // Add from
        $sql[] = 'from ('. $this->qFrom->qSql() .') ' . $this->qFromAlias;
      } else {
        // Set table
        $sql[] = 'from ' . $this->table;
      }

      // Add where
      if ($this->qWheres) {
        // Add
        $sql[] = 'where ' . implode(' and ', $this->qWheres);
      }
      // Add havings
      if ($this->qHavings) {
        // Add
        $sql[] = 'having ' . implode(' and ', $this->qHavings);
      }

      // Add order
      if ($this->qOrder) {
        // Add
        $sql[] = 'order by ' . $this->qOrder;
      }
      // Add limit
      if ($this->qLimit) {
        // add
        $sql[] = 'limit ' . $this->qLimit;
      }

      // Add unions
      if ($this->qUnions) {
        // Add each
        foreach ($this->qUnions as $query) {
          // Add
          $sql[] = 'union ' . $query->qSql();
        }
      }

      // Return
      return implode(' ', $sql);
    }

    /**
     * Get
     */
    function qGet($addToModel = true) {
      // Execute sql
      $get = DB::select(DB::raw($this->qSql()));
      // If to add to model
      if ($addToModel) {
        // Objects
        $objects = array();
        // Loop
        foreach ($get as &$row) {
          // Declare object
          $object = new static;
          // Fill with the attributes
          $object->fill((array)$row);
          // Add to objects
          $row = $object;
        }
      }
      // Return
      return $get;
    }

    /**
     * Quote string
     */
    static function qQuote($string) {
      // Return
      return DB::getPdo()->quote($string);
    }

  }