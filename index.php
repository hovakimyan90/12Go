<?php

class Calculate
{
    const PERSON_ASK_MAX_VALUE = 20;

    public $input;

    /**
     * Calculate all
     * @param string $input
     */
    public function calc(string $input)
    {
        $coors = [];

        $x_summary = 0.0;

        $y_summary = 0.0;
        // Commands must be separated with ','
        $commands = explode(',', $input);

        if (count($commands) > self::PERSON_ASK_MAX_VALUE)
        {
            throw new \InvalidArgumentException('You can not ask more than 20 people');
        }



        foreach ($commands as $command) {
            if (!trim($command)) {
                throw new \InvalidArgumentException('Command cannot be empty');
            }

            $run = new RunCommands($command);

            $end_point = $run->last_coor();

            $coors[] = $end_point;

            $x_summary += $end_point['x'];
            $y_summary += $end_point['y'];
        }

        $average = ['x' => $x_summary / count($coors), 'y' => $y_summary / count($coors)];

        $distance = 0;

        foreach ($coors as $coor) {

            $calculate = new self();
            $command_distance = $calculate->distance($coor, $average);


            if ($command_distance > $distance) {
                $distance = $command_distance;
            }
        }

        $distance = sqrt($distance);


        echo sprintf("%.5f %.5f %.5f", $average['x'], $average['y'], $distance);
    }

    /**
     * Calculate distance
     * @param array $start
     * @param array $end
     * @return int|mixed
     */
    public function distance(array $start, array $end)
    {
        return (($start['x'] - $end['x']) ** 2) + (($start['y'] - $end['y']) ** 2);
    }

}

class RunCommands
{
    const MIN_SPACE_VALUE = 4;

    protected $x_coor;

    protected $y_coor;

    protected $degree = 0.0;

    protected $actions = [];

    protected $points = [];

    public function __construct(string $command)
    {
        $modify = $this->modify($command);

        // Set command start points
        $this->x_coor = (float)array_shift($modify);
        $this->y_coor = (float)array_shift($modify);

        $this->actions = $modify;
    }

    /**
     * Get last coor
     * @return array
     */
    public function last_coor()
    {
        while (!empty($this->actions)) {

            $action = trim(array_shift($this->actions));

            $value = array_shift($this->actions);

            if (is_null($value)) {
                throw new \InvalidArgumentException("Value for action '$action' is not defined");
            } elseif ($action !== 'start' && $action !== 'walk' && $action !== 'turn') {
                throw new \InvalidArgumentException("Action '$action' is not a right command");
            }

            $this->{$action}((float)$value);
        }

        return ['x' => $this->x_coor, 'y' => $this->y_coor];
    }

    /**
     * Modify command
     * @param string $command
     * @return array
     */
    public function modify(string $command): array
    {
        $result = explode(' ', $command);

        if (count($result) < self::MIN_SPACE_VALUE) {
            throw new \InvalidArgumentException("Command '$command' is not correct");
        }

        return $result;
    }

    /**
     * Start functionality
     * @param float $degree
     * @return $this
     */
    protected function start(float $degree)
    {
        $this->degree = $degree;

        return $this;
    }

    /**
     * Walk functionality
     * @param float $distance
     * @return $this
     */
    protected function walk(float $distance)
    {
        $this->x_coor += $distance * cos(deg2rad($this->degree));
        $this->y_coor += $distance * sin(deg2rad($this->degree));

        $this->points[] = ['x' => $this->points, 'y' => $this->y_coor];

        return $this;
    }

    /**
     * Turn functionality
     * @param float $degree
     * @return $this
     */
    protected function turn(float $degree)
    {
        $this->degree += $degree;

        return $this;
    }
}



