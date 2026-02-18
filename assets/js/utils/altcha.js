/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * ALTCHA utility module.
 *
 * This module handles ALTCHA challenge fetching and solving.
 */
App.Utils.Altcha = (function () {
    /**
     * Initialize ALTCHA widget in the specified container.
     *
     * @param {String} containerId The ID of the container element.
     * @param {Function} onVerified Callback when verification completes.
     *
     * @return {Promise}
     */
    function initialize(containerId, onVerified) {
        const $container = $('#' + containerId);

        if (!$container.length) {
            return Promise.resolve();
        }

        // Add loading indicator
        $container.html(
            '<div class="altcha-loading text-muted small">' +
                '<i class="fas fa-spinner fa-spin me-2"></i>' +
                lang('loading') +
                '...</div>',
        );

        // Fetch challenge from server
        return fetchChallenge()
            .then((challenge) => {
                // Render the ALTCHA widget
                renderWidget($container, challenge, onVerified);
            })
            .catch((error) => {
                console.error('ALTCHA initialization failed:', error);
                $container.html(
                    '<div class="alert alert-warning small">' +
                        '<i class="fas fa-exclamation-triangle me-2"></i>' +
                        'CAPTCHA verification unavailable' +
                        '</div>',
                );
            });
    }

    /**
     * Fetch ALTCHA challenge from server.
     *
     * @return {Promise}
     */
    function fetchChallenge() {
        const url = App.Utils.Url.siteUrl('captcha/altcha_challenge');

        return $.get(url);
    }

    /**
     * Render the ALTCHA widget.
     *
     * @param {jQuery} $container Container element.
     * @param {Object} challenge Challenge data from server.
     * @param {Function} onVerified Callback when verification completes.
     */
    function renderWidget($container, challenge, onVerified) {
        // Create widget HTML
        const html =
            '<div class="altcha-challenge card p-3 bg-light">' +
            '<div class="d-flex align-items-center">' +
            '<button type="button" class="altcha-verify-btn btn btn-outline-primary me-3">' +
            '<i class="fas fa-robot me-2"></i>' +
            lang('verify') +
            '</button>' +
            '<span class="altcha-status text-muted small">' +
            lang('click_to_verify') +
            '</span>' +
            '</div>' +
            '<div class="altcha-progress mt-2" style="display:none;">' +
            '<div class="progress" style="height: 4px;">' +
            '<div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>' +
            '</div>' +
            '</div>' +
            '</div>';

        $container.html(html);

        // Store challenge data
        $container.data('challenge', challenge);

        // Bind click handler
        $container.find('.altcha-verify-btn').on('click', function () {
            const $btn = $(this);
            const $status = $container.find('.altcha-status');
            const $progress = $container.find('.altcha-progress');

            $btn.prop('disabled', true);
            $status.text(lang('verifying') + '...');
            $progress.show();

            solveChallenge(challenge, (progress) => {
                $progress.find('.progress-bar').css('width', progress + '%');
            })
                .then((solution) => {
                    // Create payload
                    const payload = createPayload(challenge, solution);

                    // Store in hidden input
                    $('#altcha-payload').val(payload);

                    // Update UI
                    $btn.removeClass('btn-outline-primary').addClass('btn-success').html('<i class="fas fa-check me-2"></i>' + lang('verified'));

                    $status.text(lang('verification_complete'));
                    $progress.hide();

                    if (typeof onVerified === 'function') {
                        onVerified(payload);
                    }
                })
                .catch((error) => {
                    console.error('ALTCHA solve failed:', error);
                    $btn.prop('disabled', false);
                    $status.text(lang('verification_failed'));
                    $progress.hide();
                });
        });
    }

    /**
     * Solve the ALTCHA challenge using proof-of-work.
     *
     * @param {Object} challenge Challenge data.
     * @param {Function} onProgress Progress callback.
     *
     * @return {Promise}
     */
    function solveChallenge(challenge, onProgress) {
        return new Promise((resolve, reject) => {
            const maxNumber = challenge.maxnumber || 100000;
            let number = 0;
            const batchSize = 1000;

            function processBatch() {
                const end = Math.min(number + batchSize, maxNumber);

                for (; number < end; number++) {
                    const hash = sha256(challenge.salt + number);

                    if (hash === challenge.challenge) {
                        resolve(number);
                        return;
                    }
                }

                if (number < maxNumber) {
                    if (typeof onProgress === 'function') {
                        onProgress(Math.round((number / maxNumber) * 100));
                    }
                    setTimeout(processBatch, 0);
                } else {
                    reject(new Error('Could not solve challenge'));
                }
            }

            processBatch();
        });
    }

    /**
     * SHA-256 hash function.
     *
     * @param {String} message Input string.
     *
     * @return {String} Hex hash.
     */
    function sha256(message) {
        // Use Web Crypto API if available
        if (window.crypto && window.crypto.subtle) {
            // Synchronous fallback using a simple implementation
            return sha256Sync(message);
        }
        return sha256Sync(message);
    }

    /**
     * Simple synchronous SHA-256 implementation.
     *
     * @param {String} message Input string.
     *
     * @return {String} Hex hash.
     */
    function sha256Sync(message) {
        // Use a simple SHA-256 implementation for synchronous hashing
        // This is a simplified version - for production, consider using a library

        function rightRotate(value, amount) {
            return (value >>> amount) | (value << (32 - amount));
        }

        const mathPow = Math.pow;
        const maxWord = mathPow(2, 32);
        let result = '';

        const words = [];
        const asciiBitLength = message.length * 8;

        let hash = [];
        const k = [];
        let primeCounter = 0;

        const isComposite = {};
        for (let candidate = 2; primeCounter < 64; candidate++) {
            if (!isComposite[candidate]) {
                for (let i = 0; i < 313; i += candidate) {
                    isComposite[i] = candidate;
                }
                hash[primeCounter] = (mathPow(candidate, 0.5) * maxWord) | 0;
                k[primeCounter++] = (mathPow(candidate, 1 / 3) * maxWord) | 0;
            }
        }

        message += '\x80';
        while ((message.length % 64) - 56) message += '\x00';
        for (let i = 0; i < message.length; i++) {
            const j = message.charCodeAt(i);
            if (j >> 8) return;
            words[i >> 2] |= j << (((3 - i) % 4) * 8);
        }
        words[words.length] = (asciiBitLength / maxWord) | 0;
        words[words.length] = asciiBitLength;

        for (let j = 0; j < words.length; ) {
            const w = words.slice(j, (j += 16));
            const oldHash = hash;
            hash = hash.slice(0, 8);

            for (let i = 0; i < 64; i++) {
                const w15 = w[i - 15],
                    w2 = w[i - 2];

                const a = hash[0],
                    e = hash[4];
                const temp1 =
                    hash[7] +
                    (rightRotate(e, 6) ^ rightRotate(e, 11) ^ rightRotate(e, 25)) +
                    ((e & hash[5]) ^ (~e & hash[6])) +
                    k[i] +
                    (w[i] =
                        i < 16
                            ? w[i]
                            : (w[i - 16] +
                                  (rightRotate(w15, 7) ^ rightRotate(w15, 18) ^ (w15 >>> 3)) +
                                  w[i - 7] +
                                  (rightRotate(w2, 17) ^ rightRotate(w2, 19) ^ (w2 >>> 10))) |
                              0);
                const temp2 = (rightRotate(a, 2) ^ rightRotate(a, 13) ^ rightRotate(a, 22)) + ((a & hash[1]) ^ (a & hash[2]) ^ (hash[1] & hash[2]));

                hash = [(temp1 + temp2) | 0].concat(hash);
                hash[4] = (hash[4] + temp1) | 0;
            }

            for (let i = 0; i < 8; i++) {
                hash[i] = (hash[i] + oldHash[i]) | 0;
            }
        }

        for (let i = 0; i < 8; i++) {
            for (let j = 3; j + 1; j--) {
                const b = (hash[i] >> (j * 8)) & 255;
                result += (b < 16 ? '0' : '') + b.toString(16);
            }
        }

        return result;
    }

    /**
     * Create the ALTCHA payload for submission.
     *
     * @param {Object} challenge Original challenge.
     * @param {Number} solution Solution number.
     *
     * @return {String} Base64-encoded payload.
     */
    function createPayload(challenge, solution) {
        const payload = {
            algorithm: challenge.algorithm || 'SHA-256',
            challenge: challenge.challenge,
            number: solution,
            salt: challenge.salt,
            signature: challenge.signature,
        };

        return btoa(JSON.stringify(payload));
    }

    /**
     * Get the current payload value.
     *
     * @return {String}
     */
    function getPayload() {
        return $('#altcha-payload').val() || '';
    }

    /**
     * Reset the ALTCHA widget.
     *
     * @param {String} containerId The ID of the container element.
     */
    function reset(containerId) {
        $('#altcha-payload').val('');
        initialize(containerId);
    }

    return {
        initialize,
        fetchChallenge,
        getPayload,
        reset,
    };
})();
