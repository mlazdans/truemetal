<?php declare(strict_types = 1);

final class ResKind
{
	const ARTICLE      = 1; // Tālāk var iedalīties 'article', 'reviews', 'interviews'
	const FORUM        = 2; // Tālāk var iedalīties show under 'article', 'reviews', 'interviews' vai event
	const COMMENT      = 3;
	const GALLERY      = 4;
	const GALLERY_DATA = 5;
}

// enum ResKind: int
// {
// 	case ARTICLE = 1;
// 	case FORUM = 2;
// 	case COMMENT = 3;
// 	case GALLERY = 4;
// 	case GALLERY_DATA = 5;
// }
