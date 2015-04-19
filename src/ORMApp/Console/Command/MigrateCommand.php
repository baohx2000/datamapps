<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace ORMApp\Console\Command;

use ORMApp\Console\Command;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand as MCom;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption;

/**
 * Command for executing a migration to a specified version or the latest available version.
 *
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link    www.doctrine-project.org
 * @since   2.0
 * @author  Jonathan Wage <jonwage@gmail.com>
 */
class MigrateCommand extends MCom
{
    /**
     * @var Application
     */
    static protected $odin;
    static protected $dryRun = false;
    static public function getOdinApplication() {
        return static::$odin;
    }
    static public function isDryRun() {
        return static::$dryRun;
    }

    /**
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getHelper('app')->getApplication();
        $app['Identity']->setIdentity(new UserIdentity($input->getOption('runAsUser'), $app));
        static::$odin = $app;

        static::$dryRun = $input->hasOption('dry-run') && $input->getOption('dry-run');
    }

    protected function configure()
    {
        parent::configure();
        $this->setName('contatta:migrate');
        $this->addOption('runAsUser','U',InputOption::VALUE_OPTIONAL,'User Identity to run as',0);

    }

    public function execute(InputInterface $input, OutputInterface $output) {
        /** @var EntityManagerInterface $em */
        $em = $this->getOdinApplication()['EntityManager'];
        try {
            $out = parent::execute($input,$output);
        } catch(\Exception $e) {
            if(!empty(static::$afterMigrations)) {
                foreach(array_keys(static::$afterMigrations) as $version) {
                    $em->getConnection()->delete('doctrine_migration_versions',['version'=>$version]);
                }
            }
            throw $e;
        }
        if(empty(static::$afterMigrations)) return $out;
        foreach(array_keys(static::$afterMigrations) as $version) {
            $em->getConnection()->delete('doctrine_migration_versions',['version'=>$version]);
        }
        $done = [];
        try {
            foreach(static::$afterMigrations as $version => $func) {
                $done[$version] = false;
                if($func['message']) {
                    $output->writeln($func['message']);
                } else {
                    $output->writeln("Executing after-migration for {$version}");
                }
                try {
                    $execTimeStart = microtime(true);
                    $func['closure']();
                    $done[$version] = true;
                    $totalTime     = microtime(true) - $execTimeStart;
                    $output->writeln('Took ' . sprintf('%.2f', $totalTime) . ' seconds');
                } catch(\Exception $e) {
                    $done[$version] = false;
                    /** @var LoggerInterface $log */
                    $log = $this->getOdinApplication()['monolog'];
                    $log->error("Migration {$version}  failed: ".$e->getMessage());
                    $output->writeln('<error>Exception: '.$e->getMessage().'</error>');
                    $output->writeln("<error>Trace: </error>\n".$e->getTraceAsString());
                    $log->error($e->getTraceAsString());
                    throw $e;
                }
            }
        } catch(\Exception $e) {
            throw $e;
            // should have caught all migrations above...
        } finally {
            foreach($done as $version=>$isDone) {
                if($isDone) {
                    $em->getConnection()->insert('doctrine_migration_versions',['version'=>$version]);
                }
            }
        }
    }
    private static $afterMigrations = [];

    /**
     * @param callable                                    $func
     * @param \Doctrine\DBAL\Migrations\AbstractMigration $migration
     * @param string                                      $message
     */
    public static function addAfterMigration($func, AbstractMigration $migration, $message = '') {
        $version = end(explode('\\',get_class($migration)));
        $version = substr($version,7);
        static::$afterMigrations[$version] = ['closure'=>$func,'message'=>$message];
    }
}
