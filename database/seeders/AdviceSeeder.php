<?php
// database/seeders/AdviceSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Advice;

class AdviceSeeder extends Seeder
{
    public function run()
    {
        Advice::create([
            'title' => 'Plantation de tomates',
            'content' => 'Plantez les tomates dans un sol bien drainé avec beaucoup de soleil.',
            'temperature_min' => 18,
            'temperature_max' => 30,
            'humidity_min' => 50,
            'humidity_max' => 70,
        ]);

        Advice::create([
            'title' => 'Programme d\'arrosage',
            'content' => 'Arrosez vos plantes tôt le matin pour éviter l\'évaporation.',
        ]);

        Advice::create([
            'title' => 'Plantation d\'arbres',
            'content' => 'Choisissez un emplacement avec suffisamment d\'espace pour que les racines et les branches puissent se développer. Creusez un trou deux fois plus large que la motte.',
            'temperature_min' => 10,
            'temperature_max' => 25,
            'humidity_min' => 40,
            'humidity_max' => 60,
        ]);

        Advice::create([
            'title' => 'Entretien des oliviers',
            'content' => 'Les oliviers ont besoin d\'un sol bien drainé et de beaucoup de soleil. Arrosez-les en profondeur mais peu fréquemment.',
            'temperature_min' => 15,
            'temperature_max' => 30,
            'humidity_min' => 40,
            'humidity_max' => 70,
        ]);

        Advice::create([
            'title' => 'Taille des oliviers',
            'content' => 'Taillez les oliviers à la fin du printemps ou au début de l\'été pour enlever les branches mortes ou malades et pour former l\'arbre.',
        ]);

        Advice::create([
            'title' => 'Fertilisation des oliviers',
            'content' => 'Utilisez un engrais équilibré au début du printemps. Évitez de trop fertiliser car cela peut entraîner une croissance excessive des feuilles.',
        ]);

        Advice::create([
            'title' => 'Lumière pour les plantes',
            'content' => 'Les plantes ont besoin de lumière pour la photosynthèse. Assurez-vous qu\'elles reçoivent suffisamment de lumière naturelle ou utilisez des lampes de croissance.',
        ]);

        Advice::create([
            'title' => 'Température pour les semis',
            'content' => 'Les semis ont besoin d\'une température constante pour germer. Maintenez une température de 20-25 °C pour de meilleurs résultats.',
        ]);

        Advice::create([
            'title' => 'Utilisation des pesticides',
            'content' => 'Utilisez des pesticides avec parcimonie et suivez les instructions sur l\'étiquette pour éviter de nuire aux plantes et à l\'environnement.',
        ]);

        Advice::create([
            'title' => 'Récolte des légumes',
            'content' => 'Récoltez vos légumes tôt le matin lorsque les températures sont fraîches pour maintenir leur fraîcheur plus longtemps.',
        ]);

        Advice::create([
            'title' => 'Protection contre les maladies',
            'content' => 'Pour prévenir les maladies, utilisez des produits fongicides appropriés et assurez-vous que les plantes ont suffisamment d\'espace pour respirer.',
        ]);

        Advice::create([
            'title' => 'Lutte contre les insectes',
            'content' => 'Utilisez des méthodes de lutte intégrée contre les insectes, comme les pièges à insectes et les prédateurs naturels, pour protéger vos plantes.',
        ]);

        Advice::create([
            'title' => 'Culture sous serre',
            'content' => 'L\'utilisation d\'une serre permet de prolonger la saison de croissance. Assurez-vous de ventiler régulièrement pour éviter l\'accumulation d\'humidité.',
        ]);

        Advice::create([
            'title' => 'Techniques de jardinage',
            'content' => 'Utilisez des techniques de jardinage telles que la rotation des cultures et le compostage pour améliorer la santé de votre jardin.',
        ]);
    }
}
