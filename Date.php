<?php

class Date {
    private int $year;
    private int $month;
    private int $day;

    public function __construct(int $year, int $month, int $day) {
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
    }

    public function getStr() {
        $year = $this->year;
        $month = sprintf('%02d', $this->month);
        $day = sprintf('%02d', $this->day);

        return "$day/$month/$year";
    }

    public function __toString() {
        $year = $this->year;
        $month = sprintf('%02d', $this->month);
        $day = sprintf('%02d', $this->day); 

        return "$day/$month/$year";
    }
}