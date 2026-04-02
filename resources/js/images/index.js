// Auto-export image URLs for images placed in this folder.
// Vite will return URLs for matched assets when using `import.meta.glob` with `query: '?url'`.
const modules = import.meta.glob('./*{png,jpg,jpeg,webp,svg}', { eager: true, query: '?url' });

const images = Object.entries(modules).map(([path, url]) => {
  // Vite may return the URL string directly or a module namespace object
  // with the URL as the `default` export depending on build/runtime.
  const resolved = (typeof url === 'string') ? url : (url && url.default) ? url.default : url;
  return {
    src: resolved,
    alt: path.replace('./', ''),
  };
});

export default images;
