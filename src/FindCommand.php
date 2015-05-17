<?php

namespace Vanbrabantf;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class FindCommand extends Command {

    private $finderService;

    public function __construct(FinderService $finderService)
    {
        $this->finderService = $finderService;
        parent::__construct();
    }

    public function configure()
    {
        $this   ->setName('find')
                ->setDescription('matches 2 people');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $question   = new Question('Please enter the size of the grid: ', '10');
        $gridsize   = $helper->ask($input, $output, $question);

        $question = new ConfirmationQuestion('Should one actor stand still? ', false);
        $moveSingle = $helper->ask($input, $output, $question);


        $found      = false;
        $step       = 0;

        $grid = $this->finderService->generateGrid($gridsize, $gridsize);
        $grid = $this->finderService->addActorsToGrid($grid);

        $this->outputGrid($grid, $output);

        while($found === false) {
            $playstate  = $this->finderService->moveActors($grid, $moveSingle);
            $found      = $playstate['found'];
            $grid       = $playstate['grid'];

            // give process every 100 turns
            if ($step % 100 == 0 ) {
                $this->outputGrid($grid, $output);
            }

            $step ++;
        }

        $output->writeln('Done in ' . $step . ' steps');
    }

    /**
     * @param $grid
     * @param OutputInterface $output
     */
    private function outputGrid($grid, OutputInterface $output)
    {
        $outputGridColection = $this->finderService->renderPlayingField($grid);
        $output->writeln('       ');

        foreach($outputGridColection as $outputRow) {
            $row = '';
            foreach($outputRow as $cell) {
                $row = $row . $cell;
            }

            $output->writeln($row);
        }

        $output->writeln('       ');
    }
}
