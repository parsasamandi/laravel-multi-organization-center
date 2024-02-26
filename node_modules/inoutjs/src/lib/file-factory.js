/*
 * MIT License
 *
 * Copyright (c) 2018-present Marx J. Moura
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

import FileWrapper from './file'

class FileFactory {
  constructor (file) {
    this._file = file
  }

  create () {
    if (this._file instanceof FileWrapper) {
      return this._file.toFile()
    } else if (this._file instanceof File) {
      return this._file
    } else if (this._file instanceof Blob) {
      return this._fileFromBlob(this._file)
    } else {
      return this._emptyFile()
    }
  }

  _fileFromBlob (blob) {
    return new File([blob], 'untitled', {
      type: blob.type,
      lastModified: new Date()
    })
  }

  _emptyFile () {
    const blob = new Blob([], { type: 'text/plain' })

    return new File([blob], 'untitled.txt', {
      type: blob.type,
      lastModified: new Date()
    })
  }
}

export default FileFactory
