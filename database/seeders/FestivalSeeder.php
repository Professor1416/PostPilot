<?php

namespace Database\Seeders;

use App\Models\Festival;
use Illuminate\Database\Seeder;

class FestivalSeeder extends Seeder
{
    public function run(): void
    {
        $festivals = [
            // 2026
            ['slug' => 'makar-sankranti-2026',  'name' => 'Makar Sankranti',  'name_hindi' => 'मकर संक्रांति', 'date' => '2026-01-14', 'emoji' => '🪁', 'region' => 'national',  'religion' => 'hindu'],
            ['slug' => 'republic-day-2026',      'name' => 'Republic Day',     'name_hindi' => 'गणतंत्र दिवस',  'date' => '2026-01-26', 'emoji' => '🇮🇳', 'region' => 'national', 'religion' => 'all'],
            ['slug' => 'maha-shivratri-2026',    'name' => 'Maha Shivratri',   'name_hindi' => 'महाशिवरात्रि',  'date' => '2026-02-26', 'emoji' => '🔱', 'region' => 'national',  'religion' => 'hindu'],
            ['slug' => 'holi-2026',              'name' => 'Holi',             'name_hindi' => 'होली',           'date' => '2026-03-14', 'emoji' => '🎨', 'region' => 'national',  'religion' => 'hindu'],
            ['slug' => 'ugadi-2026',             'name' => 'Ugadi',            'name_hindi' => 'उगादि',          'date' => '2026-03-19', 'emoji' => '🌸', 'region' => 'south',     'religion' => 'hindu'],
            ['slug' => 'baisakhi-2026',          'name' => 'Baisakhi',         'name_hindi' => 'बैसाखी',         'date' => '2026-04-14', 'emoji' => '🌾', 'region' => 'north',     'religion' => 'sikh'],
            ['slug' => 'eid-al-fitr-2026',       'name' => 'Eid al-Fitr',      'name_hindi' => 'ईद उल-फितर',    'date' => '2026-03-31', 'emoji' => '🌙', 'region' => 'national',  'religion' => 'muslim'],
            ['slug' => 'independence-day-2026',  'name' => 'Independence Day', 'name_hindi' => 'स्वतंत्रता दिवस','date' => '2026-08-15', 'emoji' => '🇮🇳', 'region' => 'national', 'religion' => 'all'],
            ['slug' => 'janmashtami-2026',       'name' => 'Janmashtami',      'name_hindi' => 'जन्माष्टमी',    'date' => '2026-08-23', 'emoji' => '🦚', 'region' => 'national',  'religion' => 'hindu'],
            ['slug' => 'ganesh-chaturthi-2026',  'name' => 'Ganesh Chaturthi', 'name_hindi' => 'गणेश चतुर्थी',  'date' => '2026-08-30', 'emoji' => '🐘', 'region' => 'national',  'religion' => 'hindu'],
            ['slug' => 'onam-2026',              'name' => 'Onam',             'name_hindi' => 'ओणम',            'date' => '2026-09-06', 'emoji' => '🌺', 'region' => 'south',     'religion' => 'hindu'],
            ['slug' => 'navratri-2026',          'name' => 'Navratri',         'name_hindi' => 'नवरात्रि',       'date' => '2026-09-28', 'emoji' => '💃', 'region' => 'national',  'religion' => 'hindu'],
            ['slug' => 'dussehra-2026',          'name' => 'Dussehra',         'name_hindi' => 'दशहरा',          'date' => '2026-10-08', 'emoji' => '🏹', 'region' => 'national',  'religion' => 'hindu'],
            ['slug' => 'durga-puja-2026',        'name' => 'Durga Puja',       'name_hindi' => 'दुर्गा पूजा',   'date' => '2026-10-04', 'emoji' => '🙏', 'region' => 'east',      'religion' => 'hindu'],
            ['slug' => 'diwali-2026',            'name' => 'Diwali',           'name_hindi' => 'दीवाली',         'date' => '2026-10-29', 'emoji' => '🪔', 'region' => 'national',  'religion' => 'hindu'],
            ['slug' => 'raksha-bandhan-2026',    'name' => 'Raksha Bandhan',   'name_hindi' => 'रक्षाबंधन',     'date' => '2026-08-09', 'emoji' => '🧵', 'region' => 'national',  'religion' => 'hindu'],
            ['slug' => 'eid-al-adha-2026',       'name' => 'Eid al-Adha',      'name_hindi' => 'ईद उल-अधा',     'date' => '2026-06-17', 'emoji' => '🌙', 'region' => 'national',  'religion' => 'muslim'],
            ['slug' => 'guru-nanak-2026',        'name' => 'Guru Nanak Jayanti','name_hindi' => 'गुरु नानक जयंती','date' => '2026-11-15', 'emoji' => '✨', 'region' => 'national', 'religion' => 'sikh'],
            ['slug' => 'christmas-2026',         'name' => 'Christmas',        'name_hindi' => 'क्रिसमस',        'date' => '2026-12-25', 'emoji' => '🎄', 'region' => 'national',  'religion' => 'christian'],
            ['slug' => 'new-year-2027',          'name' => 'New Year',         'name_hindi' => 'नया साल',        'date' => '2027-01-01', 'emoji' => '🎉', 'region' => 'national',  'religion' => 'all'],
            ['slug' => 'lohri-2026',             'name' => 'Lohri',            'name_hindi' => 'लोहड़ी',         'date' => '2026-01-13', 'emoji' => '🔥', 'region' => 'north',     'religion' => 'sikh'],
            ['slug' => 'pongal-2026',            'name' => 'Pongal',           'name_hindi' => 'पोंगल',          'date' => '2026-01-15', 'emoji' => '🍚', 'region' => 'south',     'religion' => 'hindu'],
            ['slug' => 'vishu-2026',             'name' => 'Vishu',            'name_hindi' => 'विशु',           'date' => '2026-04-15', 'emoji' => '🌻', 'region' => 'south',     'religion' => 'hindu'],
        ];

        foreach ($festivals as $festival) {
            Festival::updateOrCreate(
                ['slug' => $festival['slug']],
                $festival
            );
        }

        $this->command->info('Seeded ' . count($festivals) . ' Indian festivals.');
    }
}
