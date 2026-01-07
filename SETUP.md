# MyMovies - Movie & TV Show Sharing Platform

A Laravel-based social platform for sharing and discovering favorite movies and TV shows with real-time interactions.

## Features Implemented

### Core Features
- ✅ **Invite-Only Registration** - Secure beta access with invitation system
- ✅ **Authentication & Authorization** - Laravel Breeze with Tailwind CSS
- ✅ **Media Pool Management** - Centralized database of movies and TV shows
- ✅ **Smart Media Adding** - Autocomplete suggestions from existing pool
- ✅ **Privacy Controls** - Private (default) or Public visibility for each item
- ✅ **Like System** - Users can like public media with real-time updates
- ✅ **Publisher Analytics** - View who liked your content
- ✅ **Search Functionality** - Find movies and TV shows by title
- ✅ **CSV Import** - Bulk upload your media collection
- ✅ **Real-time Broadcasting** - Laravel Reverb for live like notifications

### Pages & Views
- **Home Feed** - Browse all public movies and TV shows
- **Dashboard** - Manage your personal media collection
- **My Liked Media** - View content you've liked
- **My Public Media** - See your published items and their likes
- **Invitations** - Send invitation links to new users
- **Add Media** - Smart form with autocomplete suggestions
- **Like Analytics** - See who liked your shared content

## Database Schema

### Tables
1. **users** - User accounts
2. **invitations** - Invitation tokens with expiry
3. **media_pool** - Shared pool of all movies/TV shows
4. **user_media** - User's personal media list with visibility
5. **likes** - Like relationships between users and media

### Key Relationships
- User has many UserMedia
- User has many Likes
- MediaPool has many UserMedia
- UserMedia belongs to User and MediaPool
- UserMedia has many Likes

## Getting Started

### 1. Install Dependencies
```bash
# Already done during setup
composer install
npm install
```

### 2. Build Frontend Assets
```bash
npm run build
```

### 3. Start Development Server
```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Laravel Reverb (for real-time features)
php artisan reverb:start

# Terminal 3 (optional): Queue worker
php artisan queue:work
```

### 4. Access the Application
- **URL**: http://localhost:8000
- **Initial Admin User**:
  - Email: `admin@example.com`
  - Password: `password`

## Usage Guide

### For the First User (Admin)

1. **Login** at http://localhost:8000/login
   - Email: admin@example.com
   - Password: password

2. **Send Invitations**
   - Go to "Invitations" in the navigation
   - Click "Send Invitation"
   - Enter an email address
   - Copy the generated invitation link
   - Share with your beta testers

3. **Add Your First Media**
   - Click "My Media" → "Add Media"
   - Start typing a movie title
   - If it doesn't exist in the pool, fill in details
   - Choose visibility (Private or Public)
   - Submit

4. **CSV Import** (Optional)
   - From "My Media", use the CSV import form
   - Format: `title,type,description,release_year,visibility`
   - Example: `The Matrix,movie,A computer hacker learns...,1999,public`

### For New Users

1. **Register** using the invitation link
   - Link format: `http://localhost:8000/register?invitation={TOKEN}`
   - Fill in name, email (must match invitation), password
   - Account is created immediately

2. **Browse Public Media**
   - Home page shows all public content
   - Like items by clicking the heart icon
   - Search for specific titles

3. **Manage Your Collection**
   - Add movies/TV shows from "My Media"
   - Set each item as Private or Public
   - View your liked media
   - Track likes on your public content

## Real-time Features

When Laravel Reverb is running:
- Likes appear instantly on all connected clients
- Like counts update in real-time
- Publisher sees notifications when their content is liked

## File Structure

### Key Controllers
- `InvitationController.php` - Invitation management
- `UserMediaController.php` - Media CRUD operations
- `MediaPoolController.php` - Search & autocomplete
- `LikeController.php` - Like toggle & analytics
- `HomeController.php` - Public feed

### Models
- `User.php` - User accounts with relationships
- `Invitation.php` - Invitation tokens
- `MediaPool.php` - Shared media database
- `UserMedia.php` - User's media with visibility
- `Like.php` - Like relationships

### Views
- `home.blade.php` - Public media feed
- `user-media/index.blade.php` - User dashboard
- `user-media/create.blade.php` - Add media form
- `user-media/edit.blade.php` - Edit visibility
- `user-media/liked.blade.php` - Liked media list
- `user-media/public.blade.php` - Published media
- `invitations/` - Invitation management
- `likes/likers.blade.php` - Who liked view

## Technology Stack

- **Backend**: Laravel 12.x
- **Frontend**: Blade templates + Tailwind CSS
- **Authentication**: Laravel Breeze
- **Database**: SQLite (easily switchable)
- **Real-time**: Laravel Reverb (WebSockets)
- **Broadcasting**: Reverb driver

## CSV Import Format

Create a CSV file with the following columns:

```csv
title,type,description,release_year,visibility
The Shawshank Redemption,movie,Two imprisoned men bond...,1994,public
Breaking Bad,tv_show,A high school chemistry teacher...,2008,private
Inception,movie,A thief who steals corporate secrets...,2010,public
```

**Columns**:
- `title` (required): Movie/TV show name
- `type` (required): `movie` or `tv_show`
- `description` (optional): Brief description
- `release_year` (optional): Year released
- `visibility` (optional): `private` (default) or `public`

## Future Enhancements

Potential features for production:
- Email notifications for invitations
- User profiles with avatars
- Comments on media
- Ratings system
- Recommendation engine
- Follow/unfollow users
- Activity feed
- Mobile app (API-ready)
- Advanced search filters
- Watchlist feature
- Genre categorization

## Security Notes

For production deployment:
- Change default admin credentials
- Configure proper mail driver for invitations
- Set up HTTPS
- Configure CORS properly
- Set secure session cookies
- Enable CSRF protection (already enabled)
- Use environment-specific .env files
- Set up proper queue workers
- Configure rate limiting

## Troubleshooting

### Database Issues
```bash
php artisan migrate:fresh --seed
```

### Broadcasting Not Working
1. Check Reverb is running: `php artisan reverb:start`
2. Verify .env has correct BROADCAST_CONNECTION=reverb
3. Clear config cache: `php artisan config:clear`

### Asset Issues
```bash
npm run build
php artisan view:clear
```

## Support

For issues or questions about this implementation, refer to:
- Laravel Documentation: https://laravel.com/docs
- Laravel Reverb: https://reverb.laravel.com
- Tailwind CSS: https://tailwindcss.com

---

Built with Laravel 12.x and Laravel Reverb
