// Next
import { notFound } from "next/navigation";

// Fetch
import { fetchPaginatedGenreMedia } from "@/services/movieService";

// Components
import GenrePageClient from "@/components/genres/GenrePageClient";

// Types
import { GenrePageProps } from "@/types/genre";

// Options
import { sortOptions } from "@/lib/sortOptions";

export default async function Page({ params, searchParams }: GenrePageProps) {
  /*
  | -------------------------------------------------------------------------
  | Data
  |-------------------------------------------------------------------------
  */

  const { type, genreId } = await params;
  const resolvedSearchParams = await searchParams;

  const page = Number(resolvedSearchParams.page ?? "1");
  const rawSort = resolvedSearchParams.sort_by;
  const sortBy =
    sortOptions.find((option) => option.value === rawSort)?.value ?? "popular";

  /*
  | -------------------------------------------------------------------------
  | Fetch SSR
  |-------------------------------------------------------------------------
  */

  if (type !== "movie" && type !== "tv") notFound();

  const initialData = await fetchPaginatedGenreMedia(
    type,
    genreId,
    page,
    sortBy,
  );

  /*
  | -------------------------------------------------------------------------
  | Render
  |-------------------------------------------------------------------------
  */

  return (
    <div className="md:mt-20">
      <GenrePageClient
        initialData={initialData}
        type={type}
        initialSortBy={sortBy}
      />
    </div>
  );
}
