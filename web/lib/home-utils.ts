import { fetchMediaTrailer } from "@/services/movieService";

export async function getHeroTrailers(movieId?: number, tvId?: number) {
  let movieTrailer = null;
  let tvTrailer = null;

  if (movieId) {
    try {
      movieTrailer = await fetchMediaTrailer("movie", movieId);
    } catch (error) {
      console.warn("Movie trailer not found, using fallback static.", error);
    }
  }

  if (tvId) {
    try {
      tvTrailer = await fetchMediaTrailer("tv", tvId);
    } catch (error) {
      console.warn("TV trailer not found, using fallback static.", error);
    }
  }

  return { movieTrailer, tvTrailer };
}
