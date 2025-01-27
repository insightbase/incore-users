<?php

namespace App\Console;

use App\Model\Enum\RoleEnum;
use App\Model\Role;
use App\Model\User;
use Nette\Security\Passwords;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'user:generate-test-users', description: 'Generate test users')]
class GenerateTestUsersCommand extends Command
{
    /**
     * @var string[]
     */
    private $testUsers = [
        ['firstName' => 'Jan', 'lastName' => 'Novák', 'email' => 'jan.novak@example.com'],
        ['firstName' => 'Petr', 'lastName' => 'Svoboda', 'email' => 'petr.svoboda@example.com'],
        ['firstName' => 'Tomáš', 'lastName' => 'Kučera', 'email' => 'tomas.kucera@example.com'],
        ['firstName' => 'Martin', 'lastName' => 'Veselý', 'email' => 'martin.vesely@example.com'],
        ['firstName' => 'Lukáš', 'lastName' => 'Horák', 'email' => 'lukas.horak@example.com'],
        ['firstName' => 'Michal', 'lastName' => 'Král', 'email' => 'michal.kral@example.com'],
        ['firstName' => 'Josef', 'lastName' => 'Havlíček', 'email' => 'josef.havlicek@example.com'],
        ['firstName' => 'Karel', 'lastName' => 'Bláha', 'email' => 'karel.blaha@example.com'],
        ['firstName' => 'Jaroslav', 'lastName' => 'Němec', 'email' => 'jaroslav.nemec@example.com'],
        ['firstName' => 'Václav', 'lastName' => 'Černý', 'email' => 'vaclav.cerny@example.com'],
        ['firstName' => 'Jana', 'lastName' => 'Dvořáková', 'email' => 'jana.dvorakova@example.com'],
        ['firstName' => 'Eva', 'lastName' => 'Malá', 'email' => 'eva.mala@example.com'],
        ['firstName' => 'Lucie', 'lastName' => 'Pokorná', 'email' => 'lucie.pokorna@example.com'],
        ['firstName' => 'Marie', 'lastName' => 'Zelená', 'email' => 'marie.zelena@example.com'],
        ['firstName' => 'Anna', 'lastName' => 'Říhová', 'email' => 'anna.rihova@example.com'],
        ['firstName' => 'Tereza', 'lastName' => 'Vlková', 'email' => 'tereza.vlkova@example.com'],
        ['firstName' => 'Adéla', 'lastName' => 'Havlová', 'email' => 'adela.havlova@example.com'],
        ['firstName' => 'Veronika', 'lastName' => 'Novotná', 'email' => 'veronika.novotna@example.com'],
        ['firstName' => 'Alena', 'lastName' => 'Krejčí', 'email' => 'alena.krejci@example.com'],
        ['firstName' => 'Petra', 'lastName' => 'Váchová', 'email' => 'petra.vachova@example.com'],
        ['firstName' => 'Barbora', 'lastName' => 'Beránková', 'email' => 'barbora.berankova@example.com'],
        ['firstName' => 'Zuzana', 'lastName' => 'Mašková', 'email' => 'zuzana.maskova@example.com'],
        ['firstName' => 'Ivana', 'lastName' => 'Kolářová', 'email' => 'ivana.kolarova@example.com'],
        ['firstName' => 'Helena', 'lastName' => 'Procházková', 'email' => 'helena.prochazkova@example.com'],
        ['firstName' => 'Kristýna', 'lastName' => 'Čechová', 'email' => 'kristyna.cechova@example.com'],
        ['firstName' => 'Michaela', 'lastName' => 'Hrušková', 'email' => 'michaela.hruskova@example.com'],
        ['firstName' => 'Simona', 'lastName' => 'Benešová', 'email' => 'simona.benesova@example.com'],
        ['firstName' => 'Markéta', 'lastName' => 'Jelínková', 'email' => 'marketa.jelinkova@example.com'],
        ['firstName' => 'Radka', 'lastName' => 'Slavíková', 'email' => 'radka.slavikova@example.com'],
        ['firstName' => 'Monika', 'lastName' => 'Pavlíková', 'email' => 'monika.pavlikova@example.com'],
    ];

    public function __construct(
        private readonly User $userModel,
        private readonly Role $roleModel,
        private readonly Passwords $passwords,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $role = $this->roleModel->findBySystemName(RoleEnum::ADMIN->value);

        foreach ($this->testUsers as $testUser) {
            $this->userModel->insert([
                'firstname' => $testUser['firstName'],
                'lastname' => $testUser['lastName'],
                'email' => $testUser['email'],
                'password' => $this->passwords->hash($testUser['email']),
                'role_id' => $role->id,
            ]);
        }

        return self::SUCCESS;
    }
}
