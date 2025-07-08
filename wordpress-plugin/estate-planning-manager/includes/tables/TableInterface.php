<?php
namespace EstatePlanningManager\Tables;

interface TableInterface {
    public function create($charset_collate);
    public function populate($charset_collate);
}
