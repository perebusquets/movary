let ratingEditMode = false
let originalRating

function deleteWatchDate (e) {
	const confirmed = confirm('Are you sure?')

	if (confirmed === false) {
		return
	}

	const dateFormatPhp = document.getElementById('dateFormatPhp').value
	const watchDate = document.getElementById(e.id + '-watch-date')

	const apiUrl = '/movie/' + watchDate.getAttribute('movie-id') + '/history'

	$.ajax({
		url: apiUrl,
		type: 'DELETE',
		data: JSON.stringify({
			'date': watchDate.getAttribute('date'),
			'dateFormat': dateFormatPhp
		}),
		success: function (data, textStatus, xhr) {
			window.location.reload()
		},
		error: function (xhr, textStatus, errorThrown) {
			alert('Could not delete.')
		}
	})
}

function editRating (e) {
	ratingEditMode = true

	if (originalRating === undefined) {
		originalRating = getRatingFromStars()
	}

	document.getElementById('ratingStarsSpan').classList.add('rating-edit-active')
	document.getElementById('editRatingButton').style.display = 'none'
	document.getElementById('saveRatingButton').style.display = 'inline'
	document.getElementById('resetRatingButton').style.display = 'inline'
}

function getRatingFromStars () {
	let rating = 0

	for (let ratingStarNumber = 1; ratingStarNumber <= 10; ratingStarNumber++) {
		if (document.getElementById('ratingStar' + ratingStarNumber).classList.contains('bi-star') === true) {
			break
		}

		rating = ratingStarNumber
	}

	return rating
}

function setRatingStars (newRating) {
	if (getRatingFromStars() == newRating) {
		newRating = null
	}

	for (let ratingStarNumber = 1; ratingStarNumber <= 10; ratingStarNumber++) {
		document.getElementById('ratingStar' + ratingStarNumber).classList.remove('bi-star-fill')
		document.getElementById('ratingStar' + ratingStarNumber).classList.remove('bi-star')

		if (ratingStarNumber <= newRating) {
			document.getElementById('ratingStar' + ratingStarNumber).classList.add('bi-star-fill')
		} else {
			document.getElementById('ratingStar' + ratingStarNumber).classList.add('bi-star')
		}
	}
}

function updateRatingStars (e) {
	if (ratingEditMode === false) {
		return
	}

	setRatingStars(e.id.substring(20, 10))
}

function getMovieId () {
	return document.getElementById('movieId').value
}

function saveRating () {
	let newRating = getRatingFromStars()
	let movieId = getMovieId()

	fetch('/movie/' + movieId + '/rating', {
		method: 'post',
		headers: {
			'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
		},
		body: 'rating=' + newRating
	})
		.then(function (data) {
			console.log('Request succeeded with JSON response', data)
		})
		.catch(function (error) {
			alert('Could not update rating.')
			console.log('Request failed', error)
		})

	ratingEditMode = false
	originalRating = newRating

	document.getElementById('ratingStarsSpan').classList.remove('rating-edit-active')
	document.getElementById('editRatingButton').style.display = 'inline'
	document.getElementById('saveRatingButton').style.display = 'none'
	document.getElementById('resetRatingButton').style.display = 'none'
}

function resetRating (e) {
	ratingEditMode = true

	setRatingStars(null)
	saveRating(e)

	ratingEditMode = false

	document.getElementById('ratingStarsSpan').classList.remove('rating-edit-active')
	document.getElementById('editRatingButton').style.display = 'inline'
	document.getElementById('saveRatingButton').style.display = 'none'
	document.getElementById('resetRatingButton').style.display = 'none'
}
