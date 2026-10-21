# Components restored from legacy production

The `REL1_39` branch was missing 47 extensions and two skins present in the
running `prod-legacy` deployment. These directories are restored as source
snapshots from `ghcr.io/polandballwiki/mediawiki:REL1_39-172`, image digest
`sha256:487e50bd4d9be846028d7fcc03dce77b41c7395c623c66e0f13d4d2a03325db1`.

Recovery date: 2026-10-21. Namespace: `mediawiki-production`.
The files were read from `mediawiki-67db6b867-lc7s9` and checked against
SHA-256 checksums and executable flags from `mediawiki-67db6b867-8ccrz`.
Neither `extensions` nor `skins` is backed by a volume in this deployment.

The image contains no Git metadata for these components. The restored files
preserve the deployed code, bundled dependencies, licenses and customizations;
no upstream revision is inferred. Existing component versions are unchanged.
This restores availability in the repository; it does not enable extensions,
change wiki configuration, update the database or deploy a new image.

See `legacy-components.json` for the full deployed inventory, restored paths,
manifest versions, file counts and content checksums. Checksums cover each
regular file relative to its component directory, in lexical path order, as
`path + NUL + sha256 + NUL + executable (0 or 1) + newline`, encoded as UTF-8.

## GitHub submodule URLs

The root `.gitmodules` uses GitHub mirrors directly for extensions, skins and
vendor. VisualEditor previously contained its own Gerrit URL for `lib/ve`.
To manage that URL here without a runner rewrite or another repository fork,
VisualEditor is tracked as source at its existing core pin,
`668ed3ab2851ddf758d3a878f98d61aba33707fa`. Its source matches that commit except
for the URL in `.gitmodules`. Its `lib/ve` dependency remains a submodule at
`36dd2f97210d2707117a6f47bcaa1dc14f8ee233`, declared in the root `.gitmodules`
with `https://github.com/wikimedia/VisualEditor.git`.
