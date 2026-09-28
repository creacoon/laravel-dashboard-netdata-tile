# Changelog

All notable changes to `creacoon/laravel-dashboard-netdata-tile` will be documented in this file.

## [Unreleased]

## [1.0.2] 2026-09-28

### Fixed
- Hexagons disappeared after the first Livewire poll because the morph stripped the layout set by JavaScript

### Changed
- Removed the tile title; only the online count is shown

## [1.0.0] 2026-09-23

### Added
- Netdata tile showing every node streaming to a Netdata parent as a hexagon, coloured by status
- `dashboard:fetch-data-from-netdata-api` command that fetches nodes from the parent's `/api/v2/nodes` endpoint
