export const apiConfig = {
  endpoints: {
    home: (type: string, page: number) => `/${type}/home?page=${page}`,
    mediaDetails: (id: number, type: string) => `/${type}/${id}`,
    mediaTrailer: (type: string, id: number) => `/${type}/${id}/trailer`,
    genres: (type: string) => `/${type}/genres`,
    paginatedMediasByGenre: (
      type: string,
      genreId: string | number,
      page: number,
      sortBy: string,
    ) => `/${type}/genres/${genreId}?page=${page}&sort_by=${sortBy}`,
    search: () => "/search",
  },
};
