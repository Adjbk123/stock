<?php

namespace App\DataFixtures;

use App\Entity\Approvisionnement;
use App\Entity\Categorie;
use App\Entity\Client;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use App\Entity\Vente;
use App\Entity\VenteProduit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // ── Utilisateurs (un par rôle) ──────────────────────────────────────
        $usersData = [
            ['username' => 'admin',     'roles' => ['ROLE_RESPONSABLE'], 'nom' => 'Admin',     'prenom' => 'Super',  'adresse' => '1 rue des Admins'],
            ['username' => 'tenancier', 'roles' => ['ROLE_TENANCIER'],   'nom' => 'Tenancier', 'prenom' => 'Jean',   'adresse' => '2 rue du Commerce'],
            ['username' => 'user',      'roles' => ['ROLE_USER'],        'nom' => 'Dupont',    'prenom' => 'Marie',  'adresse' => '3 avenue des Lilas'],
        ];

        $utilisateurs = [];
        foreach ($usersData as $data) {
            $u = new Utilisateur();
            $u->setUsername($data['username']);
            $u->setRoles($data['roles']);
            $u->setNom($data['nom']);
            $u->setPrenom($data['prenom']);
            $u->setAdresse($data['adresse']);
            $u->setPassword($this->hasher->hashPassword($u, 'password123'));
            $manager->persist($u);
            $utilisateurs[$data['username']] = $u;
        }

        // ── Catégories ───────────────────────────────────────────────────────
        $categoriesNoms = ['Smartphones', 'Accessoires', 'Tablettes', 'Montres connectées'];
        $categories = [];
        foreach ($categoriesNoms as $nom) {
            $cat = new Categorie();
            $cat->setIntitule($nom);
            $manager->persist($cat);
            $categories[] = $cat;
        }

        // ── Produits ─────────────────────────────────────────────────────────
        $produitsData = [
            ['nom' => 'iPhone 14',         'prix' => 999.99, 'stock' => 15, 'cat' => 0],
            ['nom' => 'Samsung Galaxy S23', 'prix' => 849.00, 'stock' => 20, 'cat' => 0],
            ['nom' => 'Coque iPhone 14',   'prix' => 19.99,  'stock' => 50, 'cat' => 1],
            ['nom' => 'Chargeur USB-C',    'prix' => 24.99,  'stock' => 40, 'cat' => 1],
            ['nom' => 'iPad Air',          'prix' => 749.00, 'stock' => 10, 'cat' => 2],
            ['nom' => 'Apple Watch SE',    'prix' => 299.00, 'stock' => 12, 'cat' => 3],
        ];

        $produits = [];
        foreach ($produitsData as $data) {
            $p = new Produit();
            $p->setNomProduit($data['nom']);
            $p->setPrix($data['prix']);
            $p->setQuantiteStock($data['stock']);
            $p->setDateAjout(new \DateTimeImmutable());
            $p->setCategorie($categories[$data['cat']]);
            $manager->persist($p);
            $produits[] = $p;
        }

        // ── Approvisionnements ───────────────────────────────────────────────
        foreach ($produits as $produit) {
            $appro = new Approvisionnement();
            $appro->setProduit($produit);
            $appro->setQuantite(rand(10, 50));
            $appro->setDateAppro(new \DateTimeImmutable('-' . rand(1, 30) . ' days'));
            $appro->setCoutUnit($produit->getPrix() * 0.6);
            $manager->persist($appro);
        }

        // ── Clients ──────────────────────────────────────────────────────────
        $clientsData = [
            ['nom' => 'Koulibaly Mamadou', 'tel' => '77 123 45 67'],
            ['nom' => 'Diallo Fatoumata',  'tel' => '76 234 56 78'],
            ['nom' => 'Ndiaye Ousmane',    'tel' => '70 345 67 89'],
        ];

        $clients = [];
        foreach ($clientsData as $data) {
            $c = new Client();
            $c->setNomsClient($data['nom']);
            $c->setTelephone($data['tel']);
            $manager->persist($c);
            $clients[] = $c;
        }

        // ── Ventes ───────────────────────────────────────────────────────────
        $ventesData = [
            ['utilisateur' => 'tenancier', 'client' => 0, 'produits' => [[0, 1], [2, 2]]],
            ['utilisateur' => 'tenancier', 'client' => 1, 'produits' => [[4, 1]]],
            ['utilisateur' => 'user',      'client' => 2, 'produits' => [[1, 2], [3, 1]]],
        ];

        foreach ($ventesData as $vData) {
            $vente = new Vente();
            $vente->setUtilisateur($utilisateurs[$vData['utilisateur']]);
            $vente->setClient($clients[$vData['client']]);
            $date = new \DateTime('-' . rand(1, 10) . ' days');
            $vente->setDateVente($date);
            $vente->setHeureVente($date);

            $total = 0.0;
            foreach ($vData['produits'] as [$idx, $qte]) {
                $vp = new VenteProduit();
                $vp->setVente($vente);
                $vp->setProduit($produits[$idx]);
                $vp->setQuantite($qte);
                $vp->setPrixUnitaire($produits[$idx]->getPrix());
                $total += $produits[$idx]->getPrix() * $qte;
                $manager->persist($vp);
            }

            $vente->setMontantTotal($total);
            $manager->persist($vente);
        }

        $manager->flush();
    }
}
