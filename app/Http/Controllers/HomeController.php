<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    //

    
   
    public function index()
    {
        // Make a GET request to the endpoint
        $response = Http::get('https://adeotidigital.com/wp-json/wp/v2/posts');

        // Retrieve the posts data
        $posts = [];

        if ($response->successful()) {
            $posts = $response->json();
            
            // Retrieve additional information (author, date, featured image) for each post
            foreach ($posts as &$post) {
                // Retrieve author
                $authorResponse = Http::get($post['_links']['author'][0]['href']);
                if ($authorResponse->successful()) {
                    $author = $authorResponse->json();
                    $post['author'] = $author['name'];
                }

                // Retrieve date
                $post['date'] = $post['date']; // Adjust as needed

                // Retrieve featured image
                $featuredImageId = $post['featured_media'];
                if (!empty($featuredImageId)) {
                    $featuredImageResponse = Http::get('https://adeotidigital.com/wp-json/wp/v2/media/' . $featuredImageId);
                    if ($featuredImageResponse->successful()) {
                        $featuredImage = $featuredImageResponse->json();
                        $post['featured_image'] = $featuredImage['source_url'];
                    }
                }
            }
        }

        $posts = array_slice($posts, 0, 3);
        // Pass the posts data to the Blade view
        return view('welcome');
    }

}
