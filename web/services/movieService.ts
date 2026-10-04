import axiosClient from "../lib/axiosClient";
import { apiConfig } from "../config/apiConfig";

// Type
import type { SearchMediaResponse } from "@/types/search";
import type { MediaType } from "@/types/movie";

/**
 * Fetch Home page data
 * @param type
 * @param page
 * @returns
 */
export const fetchHomeData = async (type: MediaType, page: number = 1) => {
  const response = await axiosClient.get(apiConfig.endpoints.home(type, page));
  return response.data;
};

/**
 * Fetch movie trailer
 * @param id
 * @returns
 */
export const fetchMediaTrailer = async (type: MediaType, id: number) => {
  const response = await axiosClient.get(
    apiConfig.endpoints.mediaTrailer(type, id),
  );
  return response.data;
};


/**
 * Fetch paginated media by genre
 * @param type
 * @param genreId
 * @param page
 * @param sortBy
 * @returns
 */
export const fetchPaginatedGenreMedia = async (
  type: MediaType,
  genreId: string | number,
  page: number = 1,
  sortBy: string = "popular",
) => {
  const response = await axiosClient.get(
    apiConfig.endpoints.paginatedMediasByGenre(type, genreId, page, sortBy),
  );

  return response.data;
};

/**
 * Search media by title or actor name.
 *
 * @param query - Search text typed by the user.
 * @param page - Results page.
 * @param signal - Abort signal used to cancel outdated requests.
 * @returns Search media results.
 */
export const fetchSearchMedia = async (
  query: string,
  page: number = 1,
  signal?: AbortSignal,
): Promise<SearchMediaResponse> => {
  const response = await axiosClient.get(apiConfig.endpoints.search(), {
    params: {
      q: query,
      page,
    },
    signal,
  });

  return response.data;
};
