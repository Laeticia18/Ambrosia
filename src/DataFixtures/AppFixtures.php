<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Option;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // ── Options ──────────────────────────────────────────────────────────
        $optionsData = [
            ['Tomates cerises', '0.00', false],
            ['Concombre', '0.00', false],
            ['Oignons rouges', '0.00', false],
            ['Croûtons', '0.00', false],
            ['Olives', '0.00', false],
            ['Maïs', '0.00', false],
            ['Supplément Avocat', '1.50', true],
            ['Supplément Saumon', '2.50', true],
            ['Supplément Feta', '1.00', true],
        ];

        $options = [];
        foreach ($optionsData as [$name, $price, $paid]) {
            $opt = new Option();
            $opt->setName($name)->setExtraPrice($price)->setIsPaid($paid);
            $manager->persist($opt);
            $options[] = $opt;
        }

        // ── Catégories ────────────────────────────────────────────────────────
        $categoriesData = [
            ['Salades Classiques', 'Nos incontournables, revisités avec soin', 'classiques'],
            ['Salades Gourmandes', 'Version généreuse avec protéines de qualité', 'gourmandes'],
            ['Salades Fraîcheur', 'Légères, vitaminées et pleines de saveurs', 'fraicheur'],
        ];

        $categories = [];
        foreach ($categoriesData as [$name, $desc, $slug]) {
            $cat = new Category();
            $cat->setName($name)->setDescription($desc)->setSlug($slug);
            $manager->persist($cat);
            $categories[$slug] = $cat;
        }

        // ── Produits ──────────────────────────────────────────────────────────
        $productsData = [
            // Classiques
            ['César', 'Laitue romaine, parmesan, anchois, sauce César maison et croûtons dorés.', '11.90', '🥗', 'classiques', [0,1,3,4]],
            ['Niçoise', 'Thon, haricots verts, tomates, olives, œuf dur sur lit de mesclun.', '12.50', '🐟', 'classiques', [0,1,2,4,5]],
            ['Grecque', 'Concombre, tomates, feta, olives noires, oignons rouges et origan.', '10.90', '🫒', 'classiques', [0,1,2,4,8]],
            ['Lyonnaise', 'Frisée, lardons grillés, œuf poché, vinaigrette moutarde à l\'ancienne.', '11.50', '🥚', 'classiques', [0,2,3]],
            // Gourmandes
            ['Caesar Chicken', 'Poulet grillé mariné, romaine, parmesan, sauce César premium.', '13.90', '🍗', 'gourmandes', [0,1,3,6]],
            ['Saumon Avocat', 'Saumon fumé, avocat crémeux, cream cheese, roquette et câpres.', '15.90', '🐟', 'gourmandes', [0,1,6,7]],
            ['Chèvre Noix', 'Chèvre chaud, noix, mâche, betterave et miel de fleurs.', '12.90', '🧀', 'gourmandes', [0,2,5,8]],
            ['Thaï Bœuf', 'Bœuf thaï mariné, vermicelles de riz, cacahuètes, menthe, coriandre.', '14.90', '🥩', 'gourmandes', [0,1,2,5,6]],
            // Fraîcheur
            ['Detox Greens', 'Épinards, kale, concombre, céleri, graines de chia, citron vert.', '9.90', '🌿', 'fraicheur', [0,1,2,5]],
            ['Summer Bowl', 'Quinoa, edamame, mangue, radis, carottes râpées, sauce sésame.', '11.90', '🌸', 'fraicheur', [0,1,5,6]],
            ['Melon Feta Menthe', 'Melon Charentais, feta émiettée, menthe fraîche, mâche et citron.', '10.50', '🍈', 'fraicheur', [0,2,4,8]],
            ['Pamplemousse Crevettes', 'Crevettes roses, pamplemousse, avocat, iceberg, vinaigrette agrumes.', '13.50', '🦐', 'fraicheur', [0,1,2,6,7]],
        ];

        foreach ($productsData as [$name, $desc, $price, $emoji, $catSlug, $optIndexes]) {
            $product = new Product();
            $product->setName($name)
                ->setDescription($desc)
                ->setBasePrice($price)
                ->setImage($emoji)
                ->setCategory($categories[$catSlug]);

            foreach ($optIndexes as $idx) {
                $product->addOption($options[$idx]);
            }

            $manager->persist($product);
        }

        $manager->flush();
    }
}
