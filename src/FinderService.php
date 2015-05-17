<?php

namespace Vanbrabantf;


class FinderService {

    /**
     * Created a grid based on the dimensions
     * @param int $x
     * @param int $y
     * @return array grid
     */
    public function generateGrid($x = 4, $y = 4)
    {
        $grid = [];
        for ($xArray = 1; $xArray <= $x; $xArray++) {
            for ($yArray = 1; $yArray <= $y; $yArray++) {
                $grid[$xArray][] = null;
            }
        }
        return $grid;
    }

    /**
     * Add the actors at a random location in the grid
     * @param $grid
     * @param int $nrOfActors
     * @return array grid
     */
    public function addActorsToGrid($grid, $nrOfActors = 2)
    {
        for ($actor = 1; $actor <= $nrOfActors; $actor++) {
            $gridSize  = $this->getGridSize($grid);
            $positionX = rand(1,$gridSize['gridWidth']);
            $positionY = rand(1,$gridSize['gridHeight']);

            if ($grid[$positionX][$positionY] === null) {
                $grid[$positionX][$positionY] = $actor;
            } else {
                $positionX = rand(1,$gridSize['gridWidth']);
                $positionY = rand(1,$gridSize['gridHeight']);
                $grid[$positionX][$positionY];
            }

        }

        return $grid;
    }


    /**
     * returns the size of the grid
     * @param $grid
     * @return array
     */
    private function getGridSize($grid)
    {
        $gridWidth  = count($grid) -1 ;
        $gridHeight = count($grid[1]) -1;

        return ['gridWidth' => $gridWidth, 'gridHeight' => $gridHeight];
    }

    /**
     * moves the actors in the grid
     * @param $grid
     * @return array
     */
    public function moveActors($grid, $moveSingle)
    {
        $newLocations = [];
        foreach($grid as $x => $actorX) {
            foreach($actorX as $y => $actorY) {
                if ($actorY !== null){
                    if ($moveSingle === true && $actorY === 1) {
                        continue;
                    }
                    $validMoves = $this->GetValidMoves($grid, $x, $y);
                    // store it for when the loop is finished, otherwise it's possible it moves more then once.
                    $newLocations[$actorY] = $validMoves[rand(0,count($validMoves) - 1)];
                    // remove the current location of the actor
                    $grid[$x][$y] = null;
                }
            }
        }
        foreach($newLocations as $actor => $newActorLocation) {
            $x = $newActorLocation['x'];
            $y = $newActorLocation['y'];

            $isFound = $this->checkIfFound($grid, $x, $y);

            if ($isFound) {
                return ['grid' => $grid, 'found' => $isFound];
            }

            $grid[$x][$y] = $actor;
        }

        return ['grid' => $grid, 'found' => false];
    }

    /**
     * Returns the possible moves the player can take from here
     * Currently only for horizontal / vertical movement
     * @param $grid
     * @param $x
     * @param $y
     * @return array
     */
    private function GetValidMoves($grid, $x, $y)
    {
        $gridSize  = $this->getGridSize($grid);
        $possibleMoves = [];
        //W
        if ($x !== 1){
            $possibleMoves[] = ['x' => $x - 1, 'y' => $y];
        }
        //N
        if ($y !== 1){
            $possibleMoves[] = ['x' => $x, 'y' => $y - 1];
        }
        //E
        if ($x = $gridSize['gridWidth'] -1){
            $possibleMoves[] = ['x' => $x + 1, 'y' => $y];
        }
        //S
        if ($y = $gridSize['gridHeight'] -1){
            $possibleMoves[] = ['x' => $x, 'y' => $y + 1];
        }
        return $possibleMoves;
    }

    /**
     * Checks if the space is occupied with another actor
     * @param $grid
     * @param $x
     * @param $y
     * @return bool
     */
    private function checkIfFound($grid, $x, $y)
    {
        if ($grid[$x][$y] !== null) {
            return true;
        }
        return false;
    }

    /**
     * Converts the grid to a collection of string, for easy display
     * @param $grid
     * @return array
     */
    public function renderPlayingField($grid)
    {

        $stringifiedGrid = [];
        foreach($grid as $x => $actorX) {
            foreach ($actorX as $y => $actorY) {
                $stringifiedGrid[$x][$y] = ".";
                if ($grid[$x][$y] !== null){
                    $stringifiedGrid[$x][$y] = $grid[$x][$y];
                }
            }
        }
        return $stringifiedGrid;
    }
}